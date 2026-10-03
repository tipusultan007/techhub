<?php

namespace App\Services;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\Order;
use App\Models\Expense;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;
use Exception;

class AccountingService
{
    /**
     * Records a sale from an Order
     */
    public function recordSale(Order $order)
    {
        DB::transaction(function () use ($order) {
            if ($order->journalEntries()->exists()) {
                return;
            }

            $journal = $order->journalEntries()->create([
                'date' => $order->created_at->toDateString(),
                'description' => "Order {$order->invoice_no} Sale",
            ]);

            $isCash = in_array(strtolower($order->payment_method), ['cash', 'cod']);
            $assetAccCode = $isCash ? '1000' : '1010'; // 1000 is Cash in Hand, 1010 is Bank
            
            $assetAcc = Account::where('code', $assetAccCode)->firstOrFail();
            $salesAcc = Account::where('code', '4000')->firstOrFail();
            $vatAcc = Account::where('code', '2100')->firstOrFail();
            $shippingAcc = Account::where('code', '4100')->firstOrFail();
            $discountAcc = Account::where('code', '6400')->firstOrFail();
            $cogsAcc = Account::where('code', '5000')->firstOrFail();
            $inventoryAcc = Account::where('code', '1300')->firstOrFail();

            // Calculate COGS
            $cogs = 0;
            foreach ($order->items as $item) {
                if ($item->variant_id && $item->variant) {
                    $cogs += $item->variant->cost_price * $item->quantity;
                } elseif ($item->product) {
                    $cogs += $item->product->cost_price * $item->quantity;
                }
            }

            if ($cogs > 0) {
                $journal->ledgerEntries()->create([
                    'account_id' => $cogsAcc->id,
                    'debit' => $cogs,
                    'credit' => 0,
                    'description' => 'Cost of Goods Sold'
                ]);
                
                $journal->ledgerEntries()->create([
                    'account_id' => $inventoryAcc->id,
                    'debit' => 0,
                    'credit' => $cogs,
                    'description' => 'Inventory reduction from sale'
                ]);
            }

            $totalReceived = $order->total;
            if ($totalReceived > 0) {
                $journal->ledgerEntries()->create([
                    'account_id' => $assetAcc->id,
                    'debit' => $totalReceived,
                    'credit' => 0,
                    'description' => 'Payment received (' . ucfirst($order->payment_method) . ')'
                ]);
            }

            if ($order->discount > 0) {
                $journal->ledgerEntries()->create([
                    'account_id' => $discountAcc->id,
                    'debit' => $order->discount,
                    'credit' => 0,
                    'description' => 'Discount applied'
                ]);
            }

            if ($order->vat_amount > 0) {
                $journal->ledgerEntries()->create([
                    'account_id' => $vatAcc->id,
                    'debit' => 0,
                    'credit' => $order->vat_amount,
                    'description' => 'VAT Output on Sales'
                ]);
            }

            if ($order->shipping_charge > 0) {
                $journal->ledgerEntries()->create([
                    'account_id' => $shippingAcc->id,
                    'debit' => 0,
                    'credit' => $order->shipping_charge,
                    'description' => 'Shipping Revenue'
                ]);
            }

            // Ensure perfect balancing regardless of how the ecommerce/POS system calculates discounts
            $productSales = $totalReceived + $order->discount - $order->vat_amount - $order->shipping_charge;

            if ($productSales > 0) {
                $journal->ledgerEntries()->create([
                    'account_id' => $salesAcc->id,
                    'debit' => 0,
                    'credit' => round($productSales, 2),
                    'description' => 'Product Sales Revenue'
                ]);
            }

            $this->validateJournalEntry($journal);
        });
    }

    /**
     * Records a Purchase Order
     */
    public function recordPurchase(PurchaseOrder $purchase)
    {
        DB::transaction(function () use ($purchase) {
            if ($purchase->journalEntries()->exists()) {
                return;
            }

            $journal = $purchase->journalEntries()->create([
                'date' => $purchase->date ?? $purchase->created_at->toDateString(),
                'description' => "Purchase Order: {$purchase->reference_no}",
            ]);

            $bankAcc = Account::where('code', '1010')->firstOrFail();
            $inventoryAcc = Account::where('code', '1300')->firstOrFail(); 
            $vatAcc = Account::where('code', '2100')->firstOrFail();

            $totalPaid = (float) $purchase->total_cost;
            $vatAmount = (float) $purchase->tax_amount;
            $netPurchase = $totalPaid - $vatAmount;

            if ($netPurchase > 0) {
                $journal->ledgerEntries()->create([
                    'account_id' => $inventoryAcc->id,
                    'debit' => $netPurchase,
                    'credit' => 0,
                    'description' => 'Inventory Purchase'
                ]);
            }

            if ($vatAmount > 0) {
                $journal->ledgerEntries()->create([
                    'account_id' => $vatAcc->id,
                    'debit' => $vatAmount,
                    'credit' => 0,
                    'description' => 'Input VAT on Purchase'
                ]);
            }

            if ($totalPaid > 0) {
                $journal->ledgerEntries()->create([
                    'account_id' => $bankAcc->id,
                    'debit' => 0,
                    'credit' => $totalPaid,
                    'description' => 'Payment for Purchase'
                ]);
            }

            $this->validateJournalEntry($journal);
        });
    }

    /**
     * Records an expense
     */
    public function recordExpense(Expense $expense)
    {
        DB::transaction(function () use ($expense) {
            if ($expense->journalEntries()->exists()) {
                return;
            }

            $journal = $expense->journalEntries()->create([
                'date' => $expense->date,
                'description' => "Expense: {$expense->title}",
            ]);

            if ($expense->account_id) {
                $bankAcc = Account::findOrFail($expense->account_id);
            } else {
                $bankAcc = Account::where('code', '1010')->firstOrFail();
            }
            $expenseAcc = Account::where('code', '6000')->firstOrFail(); 
            $vatAcc = Account::where('code', '2100')->firstOrFail();

            $totalPaid = $expense->amount;
            $vatAmount = $expense->tax_amount ?? 0;
            $netExpense = $totalPaid - $vatAmount;

            if ($netExpense > 0) {
                $journal->ledgerEntries()->create([
                    'account_id' => $expenseAcc->id,
                    'debit' => $netExpense,
                    'credit' => 0,
                    'description' => 'Expense Amount'
                ]);
            }

            if ($vatAmount > 0) {
                $journal->ledgerEntries()->create([
                    'account_id' => $vatAcc->id,
                    'debit' => $vatAmount,
                    'credit' => 0,
                    'description' => 'Input VAT on Expense'
                ]);
            }

            if ($totalPaid > 0) {
                $journal->ledgerEntries()->create([
                    'account_id' => $bankAcc->id,
                    'debit' => 0,
                    'credit' => $totalPaid,
                    'description' => 'Payment for Expense'
                ]);
            }

            $this->validateJournalEntry($journal);
        });
    }

    /**
     * Records a Capital Investment or Withdrawal
     */
    public function recordCapitalTransaction(\App\Models\CapitalTransaction $transaction)
    {
        DB::transaction(function () use ($transaction) {
            if ($transaction->journalEntries()->exists()) {
                return;
            }

            $journal = $transaction->journalEntries()->create([
                'date' => $transaction->date,
                'description' => ucfirst($transaction->type) . " - " . ($transaction->note ?? "Capital Transaction"),
            ]);

            $amount = $transaction->amount;
            $bankAccId = $transaction->bank_account_id;
            $equityAccId = $transaction->equity_account_id;

            if ($transaction->type === 'investment') {
                // Money In: Debit Bank, Credit Equity
                $journal->ledgerEntries()->create([
                    'account_id' => $bankAccId,
                    'debit' => $amount,
                    'credit' => 0,
                    'description' => 'Capital Investment Received'
                ]);
                $journal->ledgerEntries()->create([
                    'account_id' => $equityAccId,
                    'debit' => 0,
                    'credit' => $amount,
                    'description' => 'Capital Invested by Owner'
                ]);
            } else {
                // Money Out: Debit Equity, Credit Bank
                $journal->ledgerEntries()->create([
                    'account_id' => $equityAccId,
                    'debit' => $amount,
                    'credit' => 0,
                    'description' => 'Capital Withdrawal / Drawings'
                ]);
                $journal->ledgerEntries()->create([
                    'account_id' => $bankAccId,
                    'debit' => 0,
                    'credit' => $amount,
                    'description' => 'Capital Withdrawal Paid'
                ]);
            }

            $this->validateJournalEntry($journal);
        });
    }

    protected function validateJournalEntry(JournalEntry $journal)
    {
        $debits = $journal->ledgerEntries()->sum('debit');
        $credits = $journal->ledgerEntries()->sum('credit');

        if (abs($debits - $credits) > 0.02) {
            throw new Exception("Journal Entry #{$journal->id} is unbalanced. Debits: $debits, Credits: $credits");
        }
    }
}
