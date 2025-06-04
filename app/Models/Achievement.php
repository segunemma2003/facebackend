<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'nominee_id',
        'title',
        'description',
        'date',
        'achievement_image', // Uploaded file path
        'image_url', // Fallback URL
        'sort_order',
    ];

    protected $casts = [
        'date' => 'date', // Cast as date
    ];

    // Get image URL (prioritize uploaded file over fallback URL)
    public function getImageUrlAttribute()
    {
        if ($this->achievement_image) {
            return Storage::disk('public')->url($this->achievement_image);
        }
        return $this->attributes['image_url'] ?? null;
    }

    public function nominee(): BelongsTo
    {
        return $this->belongsTo(Nominee::class);
    }
}
