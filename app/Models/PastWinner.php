<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PastWinner extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'organization',
        'category',
        'achievement',
        'image_url',
        'year',
    ];

    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
