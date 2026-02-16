<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AmcItem extends Model
{
    protected $fillable = [
        'amc_id',
        'product_id',
        'product_serial_id',
        'description',
    ];

    public function amc(): BelongsTo
    {
        return $this->belongsTo(Amc::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function serial(): BelongsTo
    {
        return $this->belongsTo(ProductSerial::class, 'product_serial_id');
    }
}
