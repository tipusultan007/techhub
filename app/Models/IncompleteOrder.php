<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncompleteOrder extends Model
{
    protected $guarded = [];

    protected $casts = [
        'customer_data' => 'array',
        'cart_data' => 'array',
        'totals_data' => 'array',
        'coupon_data' => 'array',
    ];

    public function getTotalAttribute()
    {
        return $this->totals_data['total'] ?? 0;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
