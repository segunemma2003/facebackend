<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GalleryImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'gallery_event_id',
        'image_url',
        'caption',
        'sort_order',
    ];

    public function galleryEvent(): BelongsTo
    {
        return $this->belongsTo(GalleryEvent::class);
    }
}
