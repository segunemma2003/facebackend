<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralGlobalSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'address',
        'email',
        'international_phone',
        'office_hours',
        'location',
        'motto',
    ];
}
