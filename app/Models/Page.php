<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'redirect_url',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'is_active',
        'type',
        'reference_id',
        'canonical_url',
        'show_on_footer',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($page) {
            if (empty($page->slug)) {
                $page->slug = \Illuminate\Support\Str::slug($page->title);
            }
        });
    }
}
