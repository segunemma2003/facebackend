<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class GalleryImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'gallery_event_id',
        'gallery_image', // Uploaded file path
        'image_url', // Fallback URL
        'caption',
        'sort_order',
    ];

    // Get image URL (prioritize uploaded file over fallback URL)
    public function getImageUrlAttribute()
    {
        if ($this->gallery_image) {
            return Storage::disk('public')->url($this->gallery_image);
        }
        return $this->attributes['image_url'] ?? null;
    }

    public function galleryEvent(): BelongsTo
    {
        return $this->belongsTo(GalleryEvent::class);
    }
}
