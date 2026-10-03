<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AccountingController extends Controller
{
    public function accounts()
    {
        $accounts = Account::all();
        return view('admin.accounting.accounts', compact('accounts'));
    }

    public function showAccount(Account $account)
    {
        $ledgerEntries = $account->ledgerEntries()
            ->with('journalEntry.reference')
            ->join('journal_entries', 'ledger_entries.journal_entry_id', '=', 'journal_entries.id')
            ->orderBy('journal_entries.date', 'desc')
            ->orderBy('journal_entries.id', 'desc')
            ->select('ledger_entries.*')
            ->paginate(50);

        return view('admin.accounting.account_show', compact('account', 'ledgerEntries'));
    }

    public function journals()
    {
        $journals = JournalEntry::with('ledgerEntries.account', 'reference')->latest('date')->paginate(20);
        return view('admin.accounting.journals', compact('journals'));
    }

    private function calculateAccountBalance($account, $startDate = null, $endDate = null)
    {
        $query = $account->ledgerEntries();
        if ($startDate || $endDate) {
            $query->whereHas('journalEntry', function($q) use ($startDate, $endDate) {
                if ($startDate) $q->where('date', '>=', $startDate->format('Y-m-d'));
                if ($endDate) $q->where('date', '<=', $endDate->format('Y-m-d'));
            });
        }
        $debits = (float)$query->sum('debit');
        $credits = (float)$query->sum('credit');
        
        if (in_array($account->type, ['asset', 'expense'])) {
            return $debits - $credits;
        } else {
            return $credits - $debits;
        }
    }

    public function profitAndLoss(Request $request)
    {
        $data = $this->getProfitAndLossData($request);
        return view('admin.accounting.reports.pl', $data);
    }

    public function profitAndLossPdf(Request $request)
    {
        $data = $this->getProfitAndLossData($request);
        $pdf = Pdf::loadView('admin.accounting.reports.pl_pdf', $data);
        return $pdf->download('Profit-Loss-' . $data['startDate']->format('Y-m-d') . '.pdf');
    }

    private function getProfitAndLossData(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        $operatingIncomeAccounts = Account::revenue()->get()->map(function($acc) use ($startDate, $endDate) {
            $acc->computed_balance = $this->calculateAccountBalance($acc, $startDate, $endDate);
            return $acc;
        });

        $cogsAccounts = Account::where('code', '5000')->get()->map(function($acc) use ($startDate, $endDate) {
            $acc->computed_balance = $this->calculateAccountBalance($acc, $startDate, $endDate);
            return $acc;
        });

        $operatingExpenseAccounts = Account::expenses()->where('code', '!=', '5000')->get()->map(function($acc) use ($startDate, $endDate) {
            $acc->computed_balance = $this->calculateAccountBalance($acc, $startDate, $endDate);
            return $acc;
        });

        $nonOperatingIncomeAccounts = collect();
        $nonOperatingExpenseAccounts = collect();

        $totalOperatingIncome = $operatingIncomeAccounts->sum('computed_balance');
        $totalCogs = $cogsAccounts->sum('computed_balance');
        $grossProfit = $totalOperatingIncome - $totalCogs;

        $totalOperatingExpense = $operatingExpenseAccounts->sum('computed_balance');
        $operatingProfit = $grossProfit - $totalOperatingExpense;

        $totalNonOperatingIncome = 0;
        $totalNonOperatingExpense = 0;

        $netProfit = $operatingProfit + $totalNonOperatingIncome - $totalNonOperatingExpense;

        return compact(
            'startDate', 'endDate',
            'operatingIncomeAccounts', 'totalOperatingIncome',
            'cogsAccounts', 'totalCogs', 'grossProfit',
            'operatingExpenseAccounts', 'totalOperatingExpense', 'operatingProfit',
            'nonOperatingIncomeAccounts', 'totalNonOperatingIncome',
            'nonOperatingExpenseAccounts', 'totalNonOperatingExpense',
            'netProfit'
        );
    }

    public function balanceSheet(Request $request)
    {
        $data = $this->getBalanceSheetData($request);
        return view('admin.accounting.reports.balance_sheet', $data);
    }

    public function balanceSheetPdf(Request $request)
    {
        $data = $this->getBalanceSheetData($request);
        $pdf = Pdf::loadView('admin.accounting.reports.balance_sheet_pdf', $data);
        return $pdf->download('Balance-Sheet-As-Of-' . $data['asOfDate']->format('Y-m-d') . '.pdf');
    }

    private function getBalanceSheetData(Request $request)
    {
        $asOfDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        $assetAccounts = Account::assets()->get()->map(function($acc) use ($asOfDate) {
            $acc->computed_balance = $this->calculateAccountBalance($acc, null, $asOfDate);
            return $acc;
        });

        $cashEquivalents = $assetAccounts->filter(fn($a) => in_array($a->code, ['1000', '1010']));
        $accountsReceivable = $assetAccounts->filter(fn($a) => $a->code === '1200');
        $otherCurrentAssets = $assetAccounts->filter(fn($a) => !in_array($a->code, ['1000', '1010', '1200']));

        $liabilityAccounts = Account::liabilities()->get()->map(function($acc) use ($asOfDate) {
            $acc->computed_balance = $this->calculateAccountBalance($acc, null, $asOfDate);
            return $acc;
        });

        $accountsPayable = $liabilityAccounts->filter(fn($a) => $a->code === '2000');
        $otherCurrentLiabilities = $liabilityAccounts->filter(fn($a) => $a->code !== '2000');

        $equityAccounts = Account::equity()->where('code', '!=', '3100')->get()->map(function($acc) use ($asOfDate) {
            $acc->computed_balance = $this->calculateAccountBalance($acc, null, $asOfDate);
            return $acc;
        });
        
        $retainedEarningsAccount = Account::equity()->where('code', '3100')->first();
        $retainedEarnings = $retainedEarningsAccount ? $this->calculateAccountBalance($retainedEarningsAccount, null, $asOfDate) : 0;

        // Compute Current Year Earnings (Net Income up to this date)
        $revenueTotal = Account::revenue()->get()->sum(function($acc) use ($asOfDate) {
            return $this->calculateAccountBalance($acc, null, $asOfDate);
        });
        $expenseTotal = Account::expenses()->get()->sum(function($acc) use ($asOfDate) {
            return $this->calculateAccountBalance($acc, null, $asOfDate);
        });
        
        $currentYearEarnings = $revenueTotal - $expenseTotal;

        $totalAssets = $assetAccounts->sum('computed_balance');
        $totalLiabilities = $liabilityAccounts->sum('computed_balance');
        $totalEquity = $equityAccounts->sum('computed_balance') + $retainedEarnings + $currentYearEarnings;

        return compact(
            'asOfDate',
            'cashEquivalents', 'accountsReceivable', 'otherCurrentAssets', 'totalAssets',
            'accountsPayable', 'otherCurrentLiabilities', 'totalLiabilities',
            'equityAccounts', 'retainedEarnings', 'currentYearEarnings', 'totalEquity'
        );
    }
}
