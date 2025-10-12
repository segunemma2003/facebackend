<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OurTeam extends Model
{
    use HasFactory;

    protected $table = 'our_team';

    protected $fillable = [
        'image',
        'name',
        'position',
        'description',
    ];
}
