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

    public function registerMediaConversions(?\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
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
        'tax_method', 'tax_rate',
        'type', 'status', 'specifications',
        'sku', 'pno', 'barcode', 'cost_price', 'selling_price', 'sale_price', 'stock_quantity', 'alert_quantity',
        'has_serial_number', 'warranty_type', 'warranty_duration',
    ];

    public function scopeSearch($query, $term)
    {
        return $query->where('name', 'like', "%{$term}%")
            ->orWhere('description', 'like', "%{$term}%");
    }

    /**
     * Scope to exclude services and other non-physical items
     */
    public function scopePhysical($query)
    {
        return $query->where('type', '!=', 'service');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope to only include in-stock products
     */
    public function scopeInStock($query)
    {
        return $query->where(function ($q) {
            $q->where(function ($sq) {
                // Simple products with stock > 0
                $sq->where('type', '!=', 'variable')
                    ->where('stock_quantity', '>', 0);
            })->orWhere(function ($vq) {
                // Variable products with at least one in-stock variant
                $vq->where('type', 'variable')
                    ->whereHas('variants', function ($vsq) {
                        $vsq->where('stock_quantity', '>', 0);
                    });
            });
        });
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

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function purchaseOrderItems(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function returnItems(): HasMany
    {
        return $this->hasMany(ReturnItem::class);
    }

    public function quotationItems(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function deliveryChallanItems(): HasMany
    {
        return $this->hasMany(DeliveryChallanItem::class);
    }

    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
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
        return $this->variants->map(function ($variant) {
            return $variant->active_price;
        })->min();
    }

    /**
     * Check if the product is currently on sale
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

    /**
     * Check if the product can be safely deleted without breaking historical data.
     */
    public function isDeletable(): bool
    {
        return $this->orderItems()->count() === 0 &&
               $this->purchaseOrderItems()->count() === 0 &&
               $this->returnItems()->count() === 0 &&
               $this->quotationItems()->count() === 0 &&
               $this->deliveryChallanItems()->count() === 0;
    }

    /**
     * Wipe all historical transactions for this product.
     * CAUTION: This is destructive to historical business data.
     */
    public function cleanupHistory(): void
    {
        // Delete related items in all historical tables
        $this->orderItems()->delete();
        $this->purchaseOrderItems()->delete();
        $this->returnItems()->delete();
        $this->quotationItems()->delete();
        $this->deliveryChallanItems()->delete();
        $this->inventoryTransactions()->delete();

        if ($this->type === 'variable') {
            foreach ($this->variants as $variant) {
                $variant->inventoryTransactions()->delete();
                $variant->delete();
            }
        }
    }
}
