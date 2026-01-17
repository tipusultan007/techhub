<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    protected $guarded = [];

    /**
     * The SKU that was moved.
     */
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * Polymorphic relation.
     * Can link to an Order (Sale) or a PurchaseOrder (Stock In).
     */
    public function reference()
    {
        return $this->morphTo();
    }

    /**
     * The user (Admin/Staff) who performed the action.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}