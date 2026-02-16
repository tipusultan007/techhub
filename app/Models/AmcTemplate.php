<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmcTemplate extends Model
{
    protected $fillable = ['name', 'content', 'is_default'];

    protected $casts = [
        'is_default' => 'boolean',
    ];
}
