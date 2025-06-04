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
        'position',
        'location',
        'description',
        'long_bio',
        'impact_summary',
        'profile_image',
        'cover_image',
        'gallery_images',
        'image_url',
        'cover_image_url',
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
        'gallery_images' => 'array',
        'can_vote' => 'boolean',
        'is_winner' => 'boolean',
        'is_active' => 'boolean',
        'voting_percentage' => 'decimal:2',
        'votes' => 'integer',
        'year' => 'integer',
    ];

    protected $appends = ['total_votes_count'];

    // Image URL accessors - prioritize uploaded files over fallback URLs
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
        // For thumbnail - you can implement resizing logic here if needed
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

    // Relationships
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

    // Computed attributes
    public function getTotalVotesCountAttribute()
    {
        return $this->userVotes()->count();
    }

    // Business logic methods
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

    public function incrementVotes(): void
    {
        $this->increment('votes');
        $this->updateVotingPercentage();

        // Update percentages for all nominees in this category
        $this->category->nominees()->each(function ($nominee) {
            $nominee->updateVotingPercentage();
        });
    }

    // Scopes
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

    public function scopeCanVote($query)
    {
        return $query->where('can_vote', true);
    }

    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }

    public function scopeTopVoted($query, $limit = 10)
    {
        return $query->orderBy('votes', 'desc')->limit($limit);
    }

    // Boot method for model events
    protected static function boot()
    {
        parent::boot();

        // Set default year when creating
        static::creating(function ($nominee) {
            if (empty($nominee->year)) {
                $nominee->year = date('Y');
            }
        });

        // Update category voting percentages when nominee is updated
        static::updated(function ($nominee) {
            if ($nominee->wasChanged('votes')) {
                $nominee->updateVotingPercentage();
            }
        });
    }
}
