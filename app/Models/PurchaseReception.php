<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReception extends Model
{
    protected $guarded = [];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseReceptionItem::class);
    }
}
