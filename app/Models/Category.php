<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'criteria',
        'region',
        'current_nominees',
        'voting_open',
        'voting_starts_at',
        'voting_ends_at',
        'color',
        'icon',
        'featured_image', // New field for uploaded file
        'image_url', // Keep for backward compatibility
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'criteria' => 'array',
        'voting_open' => 'boolean',
        'voting_starts_at' => 'datetime',
        'voting_ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Get image URL (checks both uploaded file and fallback URL)
    public function getImageUrlAttribute()
    {
        if ($this->featured_image) {
            return Storage::disk('public')->url($this->featured_image);
        }
        return $this->attributes['image_url'] ?? null;
    }

    public function getImageThumbAttribute()
    {
        if ($this->featured_image) {
            // For now, return the same image. You can implement thumbnail generation if needed
            return Storage::disk('public')->url($this->featured_image);
        }
        return $this->attributes['image_url'] ?? null;
    }


    public function nominees(): HasMany
    {
        return $this->hasMany(Nominee::class);
    }

    public function activeNominees(): HasMany
    {
        return $this->hasMany(Nominee::class)->where('is_active', true);
    }

    public function currentYearNominees(): HasMany
    {
        return $this->hasMany(Nominee::class)->where('year', date('Y'));
    }

    public function winners(): HasMany
    {
        return $this->hasMany(Nominee::class)->where('is_winner', true);
    }

    public function getTotalVotesAttribute()
    {
        return $this->nominees()->sum('votes');
    }

    public function getIsVotingActiveAttribute(): bool
    {
        if (!$this->voting_open) return false;

        $now = now();
        $startDate = $this->voting_starts_at;
        $endDate = $this->voting_ends_at;

        return (!$startDate || $now >= $startDate) && (!$endDate || $now <= $endDate);
    }

    public function getDaysRemainingAttribute(): int
    {
        if (!$this->voting_ends_at) return 0;
        return max(0, now()->diffInDays($this->voting_ends_at, false));
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithVotingOpen($query)
    {
        return $query->where('voting_open', true);
    }
}
