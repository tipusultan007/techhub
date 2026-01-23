<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $guarded = [];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('product_image')->singleFile();
        $this->addMediaCollection('product_gallery');
    }

    public function registerMediaConversions(\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(200)
            ->height(200)
            ->sharpen(10)
            ->nonQueued();

        $this->addMediaConversion('medium')
            ->width(600)
            ->height(600)
            ->sharpen(10)
            ->nonQueued();
    }

     protected $fillable = [
        'name', 'slug', 'brand_id', 'category_id', 'description',
        'type', 'specifications',
        'sku', 'barcode', 'cost_price', 'selling_price','sale_price', 'stock_quantity', 'alert_quantity'
    ];

    /**
     * Scope to find products by name or description
     */
    public function scopeSearch($query, $term)
    {
        return $query->where('name', 'like', "%{$term}%")
                     ->orWhere('description', 'like', "%{$term}%");
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * The specific SKUs (e.g., Red/64GB, Black/128GB)
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function getActivePriceAttribute()
    {
        if ($this->type === 'simple') {
            // If sale price exists and is less than regular price
            if ($this->sale_price && $this->sale_price < $this->selling_price) {
                return $this->sale_price;
            }
            return $this->selling_price;
        }

        // For Variable, return the lowest available price
        return $this->variants->map(function($variant) {
            return $variant->active_price;
        })->min();
    }

    /**
     * Check if product is currently on sale
     */
    public function getIsOnSaleAttribute()
    {
        if ($this->type === 'simple') {
            return $this->sale_price && $this->sale_price < $this->selling_price;
        }

        // For Variable: return true if ANY variant is on sale
        return $this->variants->contains(function ($variant) {
            return $variant->sale_price && $variant->sale_price < $variant->selling_price;
        });
    }
}
