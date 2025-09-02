<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AboutSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'about_title',
        'about_sub_title',
        'about_number_recipient',
        'about_number_countries',
        'about_number_categories',
        'about_body',
        'mission',
        'vision',
    ];
}
