<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'nominee_id',
        'name',
        'role',
        'organization',
        'content',
        'testimonial_image', // New field for uploaded image
        'image_url', // Keep for backward compatibility
        'sort_order',
    ];

    // Get image URL (checks both uploaded file and fallback URL)
    public function getImageUrlAttribute()
    {
        if ($this->testimonial_image) {
            return Storage::disk('public')->url($this->testimonial_image);
        }
        return $this->attributes['image_url'] ?? null;
    }

    public function nominee(): BelongsTo
    {
        return $this->belongsTo(Nominee::class);
    }
}
