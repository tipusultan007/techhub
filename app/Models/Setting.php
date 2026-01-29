<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Setting extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = ['key', 'value'];

    /**
     * Define collections to ensure only one file exists per setting key.
     */
    public function registerMediaCollections(): void
    {
        // Define collections for known image keys
        $this->addMediaCollection('site_logo')->singleFile();
        $this->addMediaCollection('site_logo_scrolled')->singleFile();
        $this->addMediaCollection('site_logo_footer')->singleFile();
        $this->addMediaCollection('site_favicon')->singleFile();
    }

    public function registerMediaConversions(\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
        $this->addMediaConversion('optimized')
            ->format('webp') // Convert to WebP
            ->quality(100)  // Maximum quality
            ->nonQueued(); // Perform immediately
    }
}
