<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = [
        'nominee_id',
        'ip_address',
        'user_agent',
    ];

    public function nominee(): BelongsTo
    {
        return $this->belongsTo(Nominee::class);
    }
}
