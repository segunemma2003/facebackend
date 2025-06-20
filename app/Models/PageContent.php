<?php
// app/Models/PageContent.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class PageContent extends Model
{
    protected $fillable = [
        'page',
        'section',
        'key',
        'type',
        'content',
        'meta',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'meta' => 'array',
        'is_active' => 'boolean'
    ];

    // Define available pages and their sections
    public static function getPageStructure(): array
    {
        return [
            'homepage' => [
                'hero' => 'Hero Section',
                'about' => 'About Section',
                'approach' => 'Approach Section',
                'upcoming_categories' => 'Upcoming Categories',
                'award_ceremony' => 'Award Ceremony Section',
                'past_winners' => 'Past Winners Section',
                'gallery' => 'Gallery Section'
            ],
            'nominees' => [
                'hero' => 'Hero Section',
                'filters' => 'Filter Section',
                'content' => 'Main Content',
                'voting_info' => 'Voting Information'
            ],
            'categories' => [
                'hero' => 'Hero Section',
                'introduction' => 'Introduction',
                'content' => 'Main Content'
            ],
            'past_winners' => [
                'hero' => 'Hero Section',
                'introduction' => 'Introduction',
                'content' => 'Main Content'
            ],
            'gallery' => [
                'hero' => 'Hero Section',
                'introduction' => 'Introduction',
                'content' => 'Main Content'
            ],
            'approach' => [
                'hero' => 'Hero Section',
                'methodology' => 'Our Methodology',
                'process' => 'Our Process',
                'values' => 'Our Values'
            ],
            'about' => [
                'hero' => 'Hero Section',
                'story' => 'Our Story',
                'team' => 'Team Section',
                'mission' => 'Mission & Vision',
                'contact_info' => 'Contact Information'
            ],
            'contact' => [
                'hero' => 'Hero Section',
                'contact_form' => 'Contact Form',
                'contact_info' => 'Contact Information',
                'map' => 'Map Section'
            ],
            'global_settings' => [
                'social_media' => 'Social Media Links',
                'contact_info' => 'Contact Information',
                'footer' => 'Footer Content',
                'company_info' => 'Company Information'
            ]
        ];
    }

    public static function getSectionsForPage(string $page): array
    {
        return static::getPageStructure()[$page] ?? [];
    }

    // // Accessor for getting content based on type
    protected function content(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                return match($this->type) {
                    'json' => json_decode($value, true),
                    'image' => $value ? asset('storage/' . $value) : null,
                    default => $value
                };
            }
        );
    }

    // // Scope for getting page content
    // public function scopeForPage($query, $page)
    // {
    //     return $query->where('page', $page)->where('is_active', true);
    // }

    public function scopeForSection($query, $section)
    {
        return $query->where('section', $section);
    }

    // // Helper method to get content by key
    public static function getContent($page, $section, $key, $default = null)
    {
        $content = static::forPage($page)
            ->forSection($section)
            ->where('key', $key)
            ->first();

        return $content ? $content->content : $default;
    }

    // // Helper method to get all content for a section
    public static function getSectionContent($page, $section)
    {
        return static::forPage($page)
            ->forSection($section)
            ->orderBy('sort_order')
            ->get()
            ->keyBy('key')
            ->map(fn($item) => [
                'content' => $item->content,
                'type' => $item->type,
                'meta' => $item->meta
            ]);
    }
}
