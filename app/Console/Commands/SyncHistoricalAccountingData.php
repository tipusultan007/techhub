<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\Expense;
use App\Models\PurchaseOrder;
use App\Models\JournalEntry;
use App\Models\LedgerEntry;
use App\Services\AccountingService;

class SyncHistoricalAccountingData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accounting:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync historical orders and expenses into the journal entry system';

    /**
     * Execute the console command.
     */
    public function handle(AccountingService $accountingService)
    {
        $this->info('Starting historical sync (with fresh reset)...');

        // Truncate first to prevent duplicates or corrupted data
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        JournalEntry::truncate();
        LedgerEntry::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
        $this->info('Truncated existing journal entries.');

        // Sync Orders
        $orders = Order::where('status', '!=', 'cancelled')->get();
        $ordersSynced = 0;
        foreach ($orders as $order) {
            try {
                if (!$order->journalEntries()->exists()) {
                    $accountingService->recordSale($order);
                    $ordersSynced++;
                }
            } catch (\Exception $e) {
                $this->error("Failed to sync Order #{$order->id}: " . $e->getMessage());
            }
        }
        $this->info("Synced {$ordersSynced} historical orders.");

        // Sync Expenses
        $expenses = Expense::all();
        $expensesSynced = 0;
        foreach ($expenses as $expense) {
            try {
                if (!$expense->journalEntries()->exists()) {
                    $accountingService->recordExpense($expense);
                    $expensesSynced++;
                }
            } catch (\Exception $e) {
                $this->error("Failed to sync Expense #{$expense->id}: " . $e->getMessage());
            }
        }
        $this->info("Synced {$expensesSynced} historical expenses.");

        // Sync Purchases
        $purchases = PurchaseOrder::whereIn('status', ['completed', 'received', 'partial_received'])->get();
        $purchasesSynced = 0;
        foreach ($purchases as $purchase) {
            try {
                if (!$purchase->journalEntries()->exists()) {
                    $accountingService->recordPurchase($purchase);
                    $purchasesSynced++;
                }
            } catch (\Exception $e) {
                $this->error("Failed to sync Purchase #{$purchase->id}: " . $e->getMessage());
            }
        }
        $this->info("Synced {$purchasesSynced} historical purchases.");

        $this->info('Sync complete!');
    }
}
