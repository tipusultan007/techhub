<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class StandardizeVatData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:standardize-vat-data';
    protected $description = 'Recalculate Net and Tax amounts for all historical Orders and Quotations based on Product tax methods.';

    public function handle()
    {
        $this->info("Starting VAT Standardization...");

        // 1. Process Orders
        $orders = \App\Models\Order::with('items.product')->get();
        $this->info("Processing " . $orders->count() . " orders...");

        foreach ($orders as $order) {
            $orderNetSubtotal = 0;
            $orderVatAmount = 0;

            foreach ($order->items as $item) {
                $product = $item->product;
                if (!$product) {
                    $this->warn("Product missing for Order #{$order->invoice_no} Item ID: {$item->id}. Skipping recalculation for this item.");
                    continue;
                }

                $taxRate = $product->tax_rate ?? 5;
                $taxMethod = $product->tax_method ?? 'inclusive';
                $itemQty = $item->quantity;
                
                // Assuming original 'unit_price' was the price seen on product page
                $originalPrice = $item->unit_price; 
                if ($item->tax_amount > 0 && ($item->subtotal / $item->quantity) != $originalPrice) {
                    // It might already be Net if I fixed it in previous turn but with wrong logic
                    // If subtotal is Net, we need to reconstruct the original base price
                    $originalPrice = ($item->subtotal + $item->tax_amount) / $item->quantity;
                }

                $rowSubtotal = $originalPrice * $itemQty;
                $rowTax = 0;
                $netUnitPrice = 0;
                $netRowSubtotal = 0;

                if ($taxMethod === 'exclusive') {
                    $rowTax = $rowSubtotal * ($taxRate / 100);
                    $netUnitPrice = $originalPrice;
                    $netRowSubtotal = $rowSubtotal;
                } else {
                    $rowTax = $rowSubtotal - ($rowSubtotal / (1 + ($taxRate / 100)));
                    $netUnitPrice = $originalPrice / (1 + ($taxRate / 100));
                    $netRowSubtotal = $rowSubtotal - $rowTax;
                }

                $item->update([
                    'unit_price' => $netUnitPrice,
                    'subtotal'   => $netRowSubtotal,
                    'tax_rate'   => $taxRate,
                    'tax_amount' => $rowTax
                ]);

                $orderNetSubtotal += $netRowSubtotal;
                $orderVatAmount += $rowTax;
            }

            $discount = $order->discount ?? 0;
            $order->update([
                'subtotal'   => $orderNetSubtotal,
                'vat_amount' => $orderVatAmount,
                'total'      => ($orderNetSubtotal + $orderVatAmount) - $discount
            ]);
        }

        // 2. Process Quotations
        $quotations = \App\Models\Quotation::with('items.product')->get();
        $this->info("Processing " . $quotations->count() . " quotations...");

        foreach ($quotations as $quotation) {
            $quoteNetSubtotal = 0;
            $quoteVatAmount = 0;

            foreach ($quotation->items as $item) {
                $product = $item->product;
                if (!$product) continue;

                $taxRate = $product->tax_rate ?? 5;
                $taxMethod = $product->tax_method ?? 'inclusive';
                $itemQty = $item->quantity;
                
                $originalPrice = $item->unit_price;
                if ($item->tax_amount > 0 && ($item->subtotal / $item->quantity) != $originalPrice) {
                    $originalPrice = ($item->subtotal + $item->tax_amount) / $item->quantity;
                }

                $rowSubtotal = $originalPrice * $itemQty;

                if ($taxMethod === 'exclusive') {
                    $rowTax = $rowSubtotal * ($taxRate / 100);
                    $netUnitPrice = $originalPrice;
                    $netRowSubtotal = $rowSubtotal;
                } else {
                    $rowTax = $rowSubtotal - ($rowSubtotal / (1 + ($taxRate / 100)));
                    $netUnitPrice = $originalPrice / (1 + ($taxRate / 100));
                    $netRowSubtotal = $rowSubtotal - $rowTax;
                }

                $item->update([
                    'unit_price' => $netUnitPrice,
                    'subtotal'   => $netRowSubtotal,
                    'tax_rate'   => $taxRate,
                    'tax_amount' => $rowTax
                ]);

                $quoteNetSubtotal += $netRowSubtotal;
                $quoteVatAmount += $rowTax;
            }

            $discount = $quotation->discount ?? 0;
            $quotation->update([
                'subtotal'   => $quoteNetSubtotal,
                'vat_amount' => $quoteVatAmount,
                'total'      => ($quoteNetSubtotal + $quoteVatAmount) - $discount
            ]);
        }

        $this->info("VAT Standardization Complete!");
    }
}
