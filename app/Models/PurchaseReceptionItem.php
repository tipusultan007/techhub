<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReceptionItem extends Model
{
    protected $guarded = [];

    public function reception()
    {
        return $this->belongsTo(PurchaseReception::class, 'purchase_reception_id');
    }

    public function poItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class, 'purchase_order_item_id');
    }
}
