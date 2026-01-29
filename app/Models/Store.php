<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'whatsapp',
        'map_iframe',
        'hours',
        'is_main',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'hours' => 'array',
        'is_main' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSorted($query)
    {
        return $query->orderBy('is_main', 'desc')->orderBy('sort_order')->orderBy('name');
    }
}
