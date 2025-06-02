<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'can_vote' => 'boolean',
        'is_winner' => 'boolean',
        'is_active' => 'boolean',
        'voting_percentage' => 'decimal:2',
    ];

    protected $appends = ['total_votes_count'];

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

