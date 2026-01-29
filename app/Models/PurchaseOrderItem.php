<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function remaining_quantity()
    {
        return $this->quantity - $this->received_quantity;
    }

    public function is_fully_received()
    {
        return $this->received_quantity >= $this->quantity;
    }

    public function receptionItems()
    {
        return $this->hasMany(PurchaseReceptionItem::class, 'purchase_order_item_id');
    }
}
