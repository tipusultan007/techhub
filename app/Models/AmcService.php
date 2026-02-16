<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AmcService extends Model
{
    protected $fillable = [
        'amc_id',
        'scheduled_date',
        'actual_service_date',
        'status',
        'service_notes',
        'technician_id',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'actual_service_date' => 'date',
    ];

    public function amc(): BelongsTo
    {
        return $this->belongsTo(Amc::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }
}
