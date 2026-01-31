<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryChallanItem extends Model
{
    protected $guarded = [];

    public function challan()
    {
        return $this->belongsTo(DeliveryChallan::class, 'delivery_challan_id');
    }

    public function quotationItem()
    {
        return $this->belongsTo(QuotationItem::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
