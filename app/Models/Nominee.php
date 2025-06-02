<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Nominee extends Model
{
    use HasFactory;

   protected $fillable = [
        'category_id',
        'name',
        'organization',
        'description',
        'long_bio',
        'position',
        'location',
        'impact_summary',
        'profile_image', // New field for uploaded profile image
        'cover_image', // New field for uploaded cover image
        'gallery_images', // New field for uploaded gallery images (JSON array)
        'image_url', // Keep for backward compatibility
        'cover_image_url', // Keep for backward compatibility
        'video_url',
        'social_links',
        'votes',
        'voting_percentage',
        'can_vote',
        'is_winner',
        'year',
        'is_active',
    ];

    protected $casts = [
        'social_links' => 'array',
        'gallery_images' => 'array', // Cast gallery images as array
        'can_vote' => 'boolean',
        'is_winner' => 'boolean',
        'is_active' => 'boolean',
        'voting_percentage' => 'decimal:2',
    ];

    protected $appends = ['total_votes_count'];

     public function getImageUrlAttribute()
    {
        if ($this->profile_image) {
            return Storage::disk('public')->url($this->profile_image);
        }
        return $this->attributes['image_url'] ?? null;
    }

    public function getCoverImageUrlAttribute()
    {
        if ($this->cover_image) {
            return Storage::disk('public')->url($this->cover_image);
        }
        return $this->attributes['cover_image_url'] ?? null;
    }

    public function getProfileThumbAttribute()
    {
        if ($this->profile_image) {
            return Storage::disk('public')->url($this->profile_image);
        }
        return $this->attributes['image_url'] ?? null;
    }

    public function getGalleryImagesUrlsAttribute()
    {
        if ($this->gallery_images && is_array($this->gallery_images)) {
            return array_map(function($image) {
                return Storage::disk('public')->url($image);
            }, $this->gallery_images);
        }
        return [];
    }


    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function achievements(): HasMany
    {
        return $this->hasMany(Achievement::class)->orderBy('sort_order');
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class)->orderBy('sort_order');
    }

    public function userVotes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function getTotalVotesCountAttribute()
    {
        return $this->userVotes()->count();
    }

    public function updateVotingPercentage()
    {
        $categoryTotalVotes = $this->category->nominees()->sum('votes');
        $this->voting_percentage = $categoryTotalVotes > 0
            ? ($this->votes / $categoryTotalVotes) * 100
            : 0;
        $this->save();
    }

    public function hasUserVoted(string $ipAddress): bool
    {
        return $this->userVotes()->where('ip_address', $ipAddress)->exists();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCurrentYear($query)
    {
        return $query->where('year', date('Y'));
    }

    public function scopeWinners($query)
    {
        return $query->where('is_winner', true);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }
}

