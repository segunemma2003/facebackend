<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'organization',
        'country',
        'city',
        'dietary_requirements',
        'ticket_type',
        'amount',
        'reference_number',
        'status',
        'event_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'event_date' => 'datetime',
    ];

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getTicketPriceAttribute()
    {
        return match($this->ticket_type) {
            'standard' => 250,
            'vip' => 450,
            'corporate' => 1800,
            default => 250,
        };
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
