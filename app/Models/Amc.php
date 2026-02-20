<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Amc extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $fillable = [
        'customer_id',
        'site_name',
        'contract_number',
        'start_date',
        'end_date',
        'amount',
        'status',
        'frequency',
        'notes',
        'agreement_type',
        'template_id',
        'custom_agreement_content',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments');
    }

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(AmcItem::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(AmcService::class);
    }

    public function includedServices(): HasMany
    {
        return $this->hasMany(AmcIncludedService::class);
    }
}
