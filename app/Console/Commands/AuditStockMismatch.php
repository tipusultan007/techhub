<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AuditStockMismatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:audit {--fix : Automatically update stock_quantity to match expected stock}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Audit physical product stock against total purchases, total sales, and returns';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Stock Mismatch Audit...');
        $products = Product::with('variants')->get();
        $mismatches = [];

        foreach ($products as $p) {
            if ($p->type === 'variable') {
                foreach ($p->variants as $v) {
                    $purchased = (int) DB::table('purchase_order_items')
                        ->join('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_items.purchase_order_id')
                        ->where('purchase_order_items.product_variant_id', $v->id)
                        ->whereIn('purchase_orders.status', ['completed', 'received', 'partial'])
                        ->sum(DB::raw('COALESCE(NULLIF(purchase_order_items.received_quantity, 0), purchase_order_items.quantity)'));

                    $sold = (int) DB::table('order_items')
                        ->join('orders', 'orders.id', '=', 'order_items.order_id')
                        ->where('order_items.product_variant_id', $v->id)
                        ->where('orders.status', '!=', 'cancelled')
                        ->sum('order_items.quantity');

                    $returned = (int) DB::table('return_items')
                        ->join('returns', 'returns.id', '=', 'return_items.return_id')
                        ->where('return_items.product_variant_id', $v->id)
                        ->where('return_items.restock_status', 'restockable')
                        ->sum('return_items.quantity');

                    $expected = $purchased - $sold + $returned;
                    $current = (int) $v->stock_quantity;

                    if ($current !== $expected) {
                        $mismatches[] = [
                            'model' => 'variant',
                            'id' => $v->id,
                            'product_name' => $p->name . ' (' . $v->variant_name . ')',
                            'sku' => $v->sku,
                            'current' => $current,
                            'purchased' => $purchased,
                            'sold' => $sold,
                            'returned' => $returned,
                            'expected' => $expected,
                            'diff' => $current - $expected,
                        ];

                        if ($this->option('fix')) {
                            $v->update(['stock_quantity' => $expected]);
                        }
                    }
                }
            } else {
                $purchased = (int) DB::table('purchase_order_items')
                    ->join('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_items.purchase_order_id')
                    ->where('purchase_order_items.product_id', $p->id)
                    ->whereIn('purchase_orders.status', ['completed', 'received', 'partial'])
                    ->sum(DB::raw('COALESCE(NULLIF(purchase_order_items.received_quantity, 0), purchase_order_items.quantity)'));

                $sold = (int) DB::table('order_items')
                    ->join('orders', 'orders.id', '=', 'order_items.order_id')
                    ->where('order_items.product_id', $p->id)
                    ->where('orders.status', '!=', 'cancelled')
                    ->sum('order_items.quantity');

                $returned = (int) DB::table('return_items')
                    ->join('returns', 'returns.id', '=', 'return_items.return_id')
                    ->where('return_items.product_id', $p->id)
                    ->where('return_items.restock_status', 'restockable')
                    ->sum('return_items.quantity');

                $expected = $purchased - $sold + $returned;
                $current = (int) $p->stock_quantity;

                if ($current !== $expected) {
                    $mismatches[] = [
                        'model' => 'product',
                        'id' => $p->id,
                        'product_name' => $p->name,
                        'sku' => $p->sku,
                        'current' => $current,
                        'purchased' => $purchased,
                        'sold' => $sold,
                        'returned' => $returned,
                        'expected' => $expected,
                        'diff' => $current - $expected,
                    ];

                    if ($this->option('fix')) {
                        $p->update(['stock_quantity' => $expected]);
                    }
                }
            }
        }

        $headers = ['Model', 'ID', 'Product Name', 'SKU', 'DB Stock', 'Purchased', 'Sold', 'Returned', 'Expected', 'Diff'];
        $rows = array_map(function ($item) {
            return [
                $item['model'],
                $item['id'],
                $item['product_name'],
                $item['sku'],
                $item['current'],
                $item['purchased'],
                $item['sold'],
                $item['returned'],
                $item['expected'],
                $item['diff'] > 0 ? "+{$item['diff']}" : $item['diff'],
            ];
        }, $mismatches);

        if (count($mismatches) > 0) {
            $this->table($headers, $rows);
            $this->warn(count($mismatches) . " mismatched items found!");
            if ($this->option('fix')) {
                $this->info("Successfully updated all mismatched items to match expected stock.");
            } else {
                $this->comment("Run 'php artisan stock:audit --fix' to automatically sync DB stock with calculated stock.");
            }
        } else {
            $this->info("All product stock levels match total purchases, sales, and returns perfectly!");
        }

        return 0;
    }
}
