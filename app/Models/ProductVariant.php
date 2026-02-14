<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    protected $guarded = [];

    protected $fillable = [
        'product_id',
        'variant_name',
        'sku',
        'barcode',
        'cost_price',
        'selling_price',
        'sale_price',
        'stock_quantity',
        'alert_quantity',
        'options',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
    ];

    /**
     * Accessor to get full name: "iPhone 15 - Black / 128GB"
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->product->name} - {$this->variant_name}";
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * History of stock movements for this specific item
     */
    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function attributeValues()
    {
        return $this->belongsToMany(AttributeValue::class, 'product_variant_attribute_values');
    }

    public function getActivePriceAttribute()
    {
        if ($this->sale_price && $this->sale_price < $this->selling_price) {
            return $this->sale_price;
        }

        return $this->selling_price;
    }
}
