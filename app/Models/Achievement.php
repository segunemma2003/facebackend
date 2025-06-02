<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'nominee_id',
        'title',
        'description',
        'date',
        'image_url',
        'sort_order',
    ];

    public function nominee(): BelongsTo
    {
        return $this->belongsTo(Nominee::class);
    }
}
