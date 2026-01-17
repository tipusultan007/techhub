<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnOrder extends Model
{ // Renamed to avoid PHP keyword conflict
    protected $table = 'returns';
    protected $guarded = [];

    public function originalOrder()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
    public function items()
    {
        return $this->hasMany(ReturnItem::class, 'return_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}