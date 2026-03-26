<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Solution extends Model
{
    protected $fillable = [
        'title', 'slug', 'icon_class', 'summary', 'description', 'is_active', 'order',
        'meta_title', 'meta_description', 'meta_keywords'
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($solution) {
            if (empty($solution->slug)) {
                $solution->slug = \Illuminate\Support\Str::slug($solution->title);
            }
        });
    }
}
