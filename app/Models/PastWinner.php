<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PastWinner extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'organization',
        'category',
        'achievement',
        'profile_image', // Uploaded file path
        'image_url', // Fallback URL
        'year',
    ];

    protected $casts = [
        'year' => 'integer',
    ];

    // Get image URL (prioritize uploaded file over fallback URL)
    public function getImageUrlAttribute()
    {
        if ($this->profile_image) {
            return Storage::disk('public')->url($this->profile_image);
        }
        return $this->attributes['image_url'] ?? null;
    }

    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
