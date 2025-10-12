<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OurApproach extends Model
{
    use HasFactory;

    protected $table = 'our_approach';

    protected $fillable = [
        'step',
        'title',
        'description',
        'image',
    ];
}
