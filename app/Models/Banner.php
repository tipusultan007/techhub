<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Banner extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'title', 'subtitle', 'badge_text', 'button_text', 'link', 'position', 'order', 'is_active'
    ];

    public function registerMediaCollections(): void
    {
        // Ensure only one image per banner
        $this->addMediaCollection('banner_image')->singleFile();
    }
}
