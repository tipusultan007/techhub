<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $guarded = [];

    protected $casts = [
        'total' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'date' => 'date',
        'expiry_date' => 'date',
        'created_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($quotation) {
            $year = now()->year;
            $lastQuotation = Quotation::whereYear('created_at', $year)->latest()->first();

            $sequence = $lastQuotation ? (int)substr($lastQuotation->quotation_no, -5) + 1 : 1;

            $quotation->quotation_no = 'QUO-' . $year . '-' . str_pad($sequence, 5, '0', STR_PAD_LEFT);
        });
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function deliveryChallans()
    {
        return $this->hasMany(DeliveryChallan::class);
    }
}
