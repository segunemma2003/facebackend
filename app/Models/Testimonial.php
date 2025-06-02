<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'nominee_id',
        'name',
        'role',
        'organization',
        'content',
        'image_url',
        'sort_order',
    ];

    public function nominee(): BelongsTo
    {
        return $this->belongsTo(Nominee::class);
    }
}
