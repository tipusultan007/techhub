<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Order extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $guarded = [];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments');
    }

    protected $casts = [
        'subtotal' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'shipping_charge' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'created_at' => 'datetime',
        'channel' => 'string',
        'status' => 'string',
    ];
    /**
     * Boot method to generate unique Invoice Number for UAE compliance
     * Format: INV-YYYY-00001
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $year = now()->year;
            $lastOrder = Order::whereYear('created_at', $year)->latest('id')->first();

            $sequence = $lastOrder ? (int)substr($lastOrder->invoice_no, -5) + 1 : 1;
            $invoiceNo = 'INV-' . $year . '-' . str_pad($sequence, 5, '0', STR_PAD_LEFT);

            while (Order::where('invoice_no', $invoiceNo)->exists()) {
                $sequence++;
                $invoiceNo = 'INV-' . $year . '-' . str_pad($sequence, 5, '0', STR_PAD_LEFT);
            }

            $order->invoice_no = $invoiceNo;
        });
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function generateInvoiceNumber()
    {
        // Format: INV-YYYYMMDD-XXXX (e.g., INV-20240212-0001)
        $prefix = 'INV-' . date('Ymd') . '-';
        $lastOrder = self::where('invoice_no', 'like', $prefix . '%')->latest('id')->first();

        if ($lastOrder) {
            $lastNumber = intval(substr($lastOrder->invoice_no, -4));
            return $prefix . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        }

        return $prefix . '0001';
    }

    public function history()
    {
        return $this->hasMany(OrderHistory::class)->latest();
    }
}
