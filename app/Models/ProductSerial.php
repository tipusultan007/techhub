<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSerial extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'product_variant_id',
        'serial_number',
        'status', // available, sold, returned, defective
        'purchase_order_id', // Where did it come from?
        'order_id' // Where did it go?
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Scopes for easy filtering
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }
    
    public function scopeSold($query)
    {
        return $query->where('status', 'sold');
    }
}