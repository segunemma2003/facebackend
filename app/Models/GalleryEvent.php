<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GalleryEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'location',
        'event_date',
        'description',
        'attendees',
        'highlights',
        'year',
        'is_featured',
    ];

    protected $casts = [
        'event_date' => 'date',
        'is_featured' => 'boolean',
    ];

    public function images(): HasMany
    {
        return $this->hasMany(GalleryImage::class)->orderBy('sort_order');
    }

    public function getImageCountAttribute()
    {
        return $this->images()->count();
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }
}

