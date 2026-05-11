<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Notifications\LowStockNotification;
use App\Notifications\NewCustomerNotification;
use App\Notifications\NewOrderNotification;
use App\Notifications\OrderConfirmationNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class OrderService
{
    /**
     * Create a real order from checkout data.
     */
    public function createOrder(array $customerData, array $cartData, array $totalsData, string $paymentMethod, ?array $couponData = null, ?int $userId = null)
    {
        return DB::transaction(function () use ($customerData, $cartData, $totalsData, $paymentMethod, $couponData, $userId) {
            $subtotal = $totalsData['subtotal'];
            $vat_amount = $totalsData['vat'];
            $shipping_charge = $totalsData['shipping'];
            $total = $totalsData['total'];
            $discount = $totalsData['discount'];

            // 1. CRM LOGIC
            $customer = Customer::where('phone', $customerData['phone'])
                ->orWhere('email', $customerData['email'])
                ->first();

            $fullName = $customerData['first_name'] . ' ' . $customerData['last_name'];

            if (!$customer) {
                $customer = Customer::create([
                    'name' => $fullName,
                    'email' => $customerData['email'],
                    'phone' => $customerData['phone'],
                    'address' => $customerData['address'] . ', ' . $customerData['city'],
                ]);

                // Notify Admin about new customer registration
                User::role(['Admin', 'Super Admin'])->get()->each->notify(new NewCustomerNotification($customer));
            } else {
                $customer->update([
                    'address' => $customerData['address'] . ', ' . $customerData['city']
                ]);
            }

            // 2. CREATE ORDER
            $order = Order::create([
                'invoice_no'       => Order::generateInvoiceNumber(),
                'channel'          => 'online',
                'user_id'          => $userId,
                'customer_id'      => $customer->id,
                'customer_name'    => $fullName,
                'guest_email'      => $customerData['email'],
                'guest_phone'      => $customerData['phone'],
                'shipping_address' => $customerData['address'],
                'shipping_city'    => $customerData['city'],
                'shipping_area'    => $customerData['area'] ?? null,
                'subtotal'         => $subtotal,
                'vat_amount'       => $vat_amount,
                'shipping_charge'  => $shipping_charge,
                'discount'         => $discount,
                'total'            => $total,
                'payment_method'   => $paymentMethod,
                'paid_amount'      => 0,
                'due_amount'       => $total,
                'status'           => 'pending',
                'notes'            => $couponData ? 'Coupon: ' . $couponData['code'] : null,
            ]);

            // Notify Admin about new order
            User::role(['Admin', 'Super Admin'])->get()->each->notify(new NewOrderNotification($order));

            // Notify Customer about new order
            if ($customerData['email']) {
                Notification::route('mail', $customerData['email'])
                    ->notify(new OrderConfirmationNotification($order));
            }

            // Increment Coupon Usage
            if ($couponData) {
                \App\Models\Coupon::where('id', $couponData['id'])->increment('uses');
            }

            // 3. CREATE ORDER ITEMS
            foreach ($cartData as $item) {
                $itemPrice = $item['price'];
                $itemQty = $item['quantity'];
                $itemRate = $item['tax_rate'] ?? 5;
                $itemMethod = $item['tax_method'] ?? 'inclusive';

                $itemGrossBase = $itemPrice * $itemQty;

                if ($itemMethod === 'exclusive') {
                    $itemVat = $itemGrossBase * ($itemRate / 100);
                    $itemNet = $itemGrossBase;
                    $unitPrice = $itemPrice;
                } else {
                    $itemVat = $itemGrossBase - ($itemGrossBase / (1 + ($itemRate / 100)));
                    $itemNet = $itemGrossBase - $itemVat;
                    $unitPrice = $itemPrice / (1 + ($itemRate / 100));
                }

                OrderItem::create([
                    'order_id'           => $order->id,
                    'product_id'         => $item['product_id'],
                    'product_variant_id' => $item['variant_id'] ?? null,
                    'product_name'       => $item['name'],
                    'quantity'           => $item['quantity'],
                    'unit_price'         => $unitPrice,
                    'subtotal'           => $itemNet,
                    'tax_rate'           => $itemRate,
                    'tax_amount'         => $itemVat,
                ]);

                // Deduct Stock
                if (isset($item['variant_id'])) {
                    $variant = ProductVariant::find($item['variant_id']);
                    if ($variant) {
                        $variant->decrement('stock_quantity', $item['quantity']);
                        if ($variant->stock_quantity <= $variant->alert_quantity) {
                            User::role(['Admin', 'Super Admin'])->get()->each->notify(new LowStockNotification($variant->product, $variant->stock_quantity));
                        }
                    }
                } else {
                    $product = Product::find($item['product_id']);
                    if ($product) {
                        $product->decrement('stock_quantity', $item['quantity']);
                        if ($product->stock_quantity <= $product->alert_quantity) {
                            User::role(['Admin', 'Super Admin'])->get()->each->notify(new LowStockNotification($product, $product->stock_quantity));
                        }
                    }
                }
            }

            return $order;
        });
    }
}
