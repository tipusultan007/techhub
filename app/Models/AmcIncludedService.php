<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmcIncludedService extends Model
{
    protected $fillable = [
        'amc_id',
        'service_name',
        'description',
    ];

    public function amc()
    {
        return $this->belongsTo(Amc::class);
    }
}
