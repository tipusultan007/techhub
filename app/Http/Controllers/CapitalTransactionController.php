<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\CapitalTransaction;
use Illuminate\Http\Request;
use App\Services\AccountingService;

class CapitalTransactionController extends Controller
{
    public function index()
    {
        $transactions = CapitalTransaction::with(['bankAccount', 'equityAccount'])->latest('date')->paginate(15);
        $bankAccounts = Account::where('type', 'asset')->get(); // Cash and Bank
        $equityAccounts = Account::where('type', 'equity')->get();

        return view('admin.capital.index', compact('transactions', 'bankAccounts', 'equityAccounts'));
    }

    public function store(Request $request, AccountingService $accountingService)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:investment,withdrawal',
            'amount' => 'required|numeric|min:0',
            'bank_account_id' => 'required|exists:accounts,id',
            'equity_account_id' => 'required|exists:accounts,id',
            'reference_no' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        $transaction = CapitalTransaction::create($validated);

        $accountingService->recordCapitalTransaction($transaction);

        return back()->with('success', 'Transaction recorded successfully.');
    }

    public function update(Request $request, CapitalTransaction $capitalTransaction, AccountingService $accountingService)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:investment,withdrawal',
            'amount' => 'required|numeric|min:0',
            'bank_account_id' => 'required|exists:accounts,id',
            'equity_account_id' => 'required|exists:accounts,id',
            'reference_no' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        $capitalTransaction->update($validated);

        // Re-record Accounting Entry
        $capitalTransaction->journalEntries()->delete();
        $accountingService->recordCapitalTransaction($capitalTransaction);

        return back()->with('success', 'Transaction updated successfully.');
    }

    public function destroy(CapitalTransaction $capitalTransaction)
    {
        $capitalTransaction->journalEntries()->delete();
        $capitalTransaction->delete();

        return back()->with('success', 'Transaction deleted successfully.');
    }
}
