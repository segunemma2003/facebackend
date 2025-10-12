<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvisoryBoard extends Model
{
    use HasFactory;

    protected $table = 'advisory_board';

    protected $fillable = [
        'name',
        'title',
        'region',
        'expertise',
        'image',
    ];
}
