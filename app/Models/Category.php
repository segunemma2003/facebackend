<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'image_url',
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
