<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    protected $fillable = [
        'menu_id', 'label', 'url', 'type', 'model_id', 'parent_id', 'order', 'target'
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('order');
    }

    public function childrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive');
    }

    /**
     * Get the actual URL for the menu item based on its type.
     */
    public function getUrlAttribute($value)
    {
        if ($this->type === 'custom') {
            return $value;
        }

        switch ($this->type) {
            case 'category':
                $category = Category::find($this->model_id);
                return $category ? route('category.show', ['slug' => $category->slug]) : '#';
            case 'brand':
                $brand = Brand::find($this->model_id);
                return $brand ? route('brand.show', ['slug' => $brand->slug]) : '#';
            case 'page':
                $page = Page::find($this->model_id);
                return $page ? route('pages.show', ['slug' => $page->slug]) : '#';
            case 'solution':
                $solution = Solution::find($this->model_id);
                return $solution ? route('solutions.show', ['slug' => $solution->slug]) : '#';
            default:
                return '#';
        }
    }
}
