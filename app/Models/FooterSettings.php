<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FooterSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'facebook_link',
        'is_facebook',
        'twitter_link',
        'is_twitter',
        'instagram_link',
        'is_instagram',
        'footer_text',
    ];

    protected $casts = [
        'is_facebook' => 'boolean',
        'is_twitter' => 'boolean',
        'is_instagram' => 'boolean',
    ];
}
