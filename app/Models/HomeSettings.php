<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'hero_title',
        'hero_description',
        'current_program_title',
        'current_program_description',
        'coming_soon',
        'timer',
        'event_date',
        'is_button',
        'button_text',
        'button_link',
        'about_title',
        'about_description',
        'section_face_1',
        'section_face_2',
        'section_pics',
    ];

    protected $casts = [
        'coming_soon' => 'boolean',
        'timer' => 'boolean',
        'is_button' => 'boolean',
        'event_date' => 'datetime',
        'section_pics' => 'array',
    ];

    // Handle array to string conversion for all fields
    public function setAttribute($key, $value)
    {
        // Convert arrays to JSON for database storage
        if (is_array($value) && in_array($key, ['section_pics'])) {
            $value = json_encode($value);
        }
        
        // Convert arrays to strings for text fields
        if (is_array($value) && in_array($key, ['section_face_1', 'section_face_2', 'hero_description', 'current_program_description', 'about_description'])) {
            $value = is_string($value) ? $value : implode(' ', $value);
        }
        
        return parent::setAttribute($key, $value);
    }
}
