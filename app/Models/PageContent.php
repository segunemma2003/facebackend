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
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    // Define available pages and their sections
    public static function getPageStructure(): array
    {
        return [
            'homepage' => [
                'hero' => 'Hero Section',
                'trending_ticker' => 'Trending Ticker',
                'about' => 'About Section',
                'approach' => 'Approach Section',
                'upcoming_categories' => 'Award Categories',
                'gallery_section' => 'Event Gallery',
                'past_winners' => 'Past Winners Section',
                'award_ceremony' => 'Award Ceremony Section'
            ],
            'nominees' => [
                'hero' => 'Hero Section',
                'countdown_timer' => 'Countdown Timer',
                'category_filter' => 'Category Filter',
                'nominees_grid' => 'Nominees Grid',
                'voting_actions' => 'Voting Actions',
                'call_to_action' => 'Call to Action',
                'loading_states' => 'Loading States',
                'stats_labels' => 'Statistics Labels'
            ],
            'about' => [
                'hero' => 'Hero Section',
                'our_story' => 'Our Story',
                'success_stories' => 'Success Stories',
                'team' => 'Team Section',
                'call_to_action' => 'Call to Action'
            ],
            'projects' => [
    'hero' => 'Hero Section',
    'introduction' => 'Introduction Section',
    'for_homeless' => 'For the Homeless Projects',
    'for_women' => 'For Women Projects',
    'farming_food_justice' => 'Farming & Food Justice Projects',
    'social_justice' => 'Social Justice Projects',
    'call_to_action' => 'Call to Action Section'
],
            'approach' => [
                'hero' => 'Hero Section',
                'introduction' => 'Introduction',
                'process_section' => 'Process Section',
                'process_steps' => 'Process Steps',
                'call_to_action' => 'Call to Action'
            ],
            'award_process' => [
                'hero' => 'Hero Section',
                'process_timeline' => 'Process Timeline',
                'call_to_action' => 'Call to Action'
            ],
            'categories' => [
                'hero' => 'Hero Section',
                'regional_navigation' => 'Regional Navigation',
                'categories_grid' => 'Categories Grid',
                'category_card_labels' => 'Category Card Labels',
                'voting_messages' => 'Voting Messages',
                'loading_states' => 'Loading States',
                'call_to_action' => 'Call to Action'
            ],
            'contact' => [
                'hero' => 'Hero Section',
                'contact_form' => 'Contact Form',
                'contact_information' => 'Contact Information',
                'form_messages' => 'Form Messages',
                'map_section' => 'Map Section',
                'faq_cta' => 'FAQ Call to Action'
            ],
            'gallery' => [
                'hero' => 'Hero Section',
                'year_selector' => 'Year Selector',
                'gallery_content' => 'Gallery Content',
                'loading_states' => 'Loading States',
                'call_to_action' => 'Call to Action'
            ],
            'impact_stories' => [
                'hero' => 'Hero Section',
                'stories_section' => 'Stories Section'
            ],
            'nominee_profile' => [
                'navigation' => 'Navigation',
                'profile_sections' => 'Profile Sections',
                'voting_actions' => 'Voting Actions',
                'tabs' => 'Tabs',
                'social_sharing' => 'Social Sharing',
                'error_states' => 'Error States'
            ],
            'past_winners' => [
                'hero' => 'Hero Section',
                'filters_section' => 'Filters Section',
                'winners_grid' => 'Winners Grid',
                'loading_states' => 'Loading States',
                'call_to_action' => 'Call to Action'
            ],
            'registration' => [
                'hero' => 'Hero Section',
                'progress_steps' => 'Progress Steps',
                'success_messages' => 'Success Messages',
                'event_details' => 'Event Details'
            ],
            'global_settings' => [
                'social_media' => 'Social Media Links',
                'contact_info' => 'Contact Information',
                'footer' => 'Footer Content',
                'company_info' => 'Company Information'
            ]
        ];
    }

    // Get all available pages
    public static function getAvailablePages(): array
    {
        return array_keys(static::getPageStructure());
    }

    // Get sections for a specific page
    public static function getSectionsForPage(string $page): array
    {
        return static::getPageStructure()[$page] ?? [];
    }

    // Get all sections across all pages (useful for admin interfaces)
    public static function getAllSections(): array
    {
        $allSections = [];
        foreach (static::getPageStructure() as $page => $sections) {
            foreach ($sections as $sectionKey => $sectionLabel) {
                $allSections[$page . '.' . $sectionKey] = $page . ' - ' . $sectionLabel;
            }
        }
        return $allSections;
    }

    // Scope for filtering by page
    public function scopeByPage($query, $page)
    {
        return $query->where('page', $page);
    }

    // Scope for filtering by section
    public function scopeBySection($query, $section)
    {
        return $query->where('section', $section);
    }

    // Scope for active content only
    public function scopeActiveContent($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for ordered content
    public function scopeOrderedBySort($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at');
    }

    // Raw content accessor for Filament (bypasses custom formatting)
    public function getRawContentAttribute()
    {
        return $this->attributes['content'] ?? null;
    }

    // Formatted content accessor for frontend use
    public function getFormattedContentAttribute()
    {
        return match($this->type) {
            'json' => json_decode($this->attributes['content'], true),
            'boolean' => filter_var($this->attributes['content'], FILTER_VALIDATE_BOOLEAN),
            'number' => is_numeric($this->attributes['content']) ? (float) $this->attributes['content'] : $this->attributes['content'],
            'image' => $this->attributes['content'] ? (str_starts_with($this->attributes['content'], 'http') ? $this->attributes['content'] : asset('storage/' . $this->attributes['content'])) : null,
            'url' => $this->attributes['content'],
            default => $this->attributes['content']
        };
    }

    // Keep the original content accessor but make it less aggressive


    // Helper method to get content by key
    public static function getContent($page, $section, $key, $default = null)
    {
        $content = static::byPage($page)
            ->bySection($section)
            ->where('key', $key)
            ->where('is_active', true)
            ->first();

        return $content ? $content->formatted_content : $default;
    }

    // Helper method to get all content for a section
    public static function getSectionContent($page, $section)
    {
        return static::byPage($page)
            ->bySection($section)
            ->where('is_active', true)
            ->orderedBySort()
            ->get()
            ->keyBy('key')
            ->map(fn($item) => [
                'content' => $item->formatted_content,
                'type' => $item->type,
                'meta' => $item->meta,
                'sort_order' => $item->sort_order
            ]);
    }

    // Helper method to get all content for a page
    public static function getPageContent($page)
    {
        $content = static::byPage($page)
            ->where('is_active', true)
            ->orderedBySort()
            ->get()
            ->groupBy('section');

        $result = [];
        foreach ($content as $section => $items) {
            $result[$section] = $items->keyBy('key')->map(fn($item) => [
                'content' => $item->formatted_content,
                'type' => $item->type,
                'meta' => $item->meta,
                'sort_order' => $item->sort_order
            ]);
        }

        return $result;
    }

    // Helper method to get global settings
    public static function getGlobalSettings($section = null)
    {
        $query = static::byPage('global_settings')->where('is_active', true);

        if ($section) {
            $query->bySection($section);
        }

        return $query->orderedBySort()
            ->get()
            ->groupBy('section')
            ->map(function ($items) {
                return $items->keyBy('key')->map(fn($item) => [
                    'content' => $item->formatted_content,
                    'type' => $item->type,
                    'meta' => $item->meta,
                    'sort_order' => $item->sort_order
                ]);
            });
    }

    // Method to validate if a page/section combination exists
    public static function isValidPageSection($page, $section): bool
    {
        return isset(static::getPageStructure()[$page][$section]);
    }

    // Method to get content types used in the system
    public static function getContentTypes(): array
    {
        return [
            'text' => 'Text',
            'html' => 'HTML',
            'json' => 'JSON',
            'image' => 'Image',
            'url' => 'URL',
            'boolean' => 'Boolean',
            'number' => 'Number'
        ];
    }

    // Method to get the full page label
    public function getPageLabelAttribute(): string
    {
        $labels = [
            'homepage' => 'Homepage',
            'nominees' => 'Nominees',
            'about' => 'About Us',
            'approach' => 'Our Approach',
            'award_process' => 'Award Process',
            'categories' => 'Categories',
            'contact' => 'Contact',
            'gallery' => 'Gallery',
            'impact_stories' => 'Impact Stories',
            'nominee_profile' => 'Nominee Profile',
            'past_winners' => 'Past Winners',
            'registration' => 'Registration',
            'global_settings' => 'Global Settings'
        ];

        return $labels[$this->page] ?? ucfirst($this->page);
    }

    // Method to get the full section label
    public function getSectionLabelAttribute(): string
    {
        $sections = static::getSectionsForPage($this->page);
        return $sections[$this->section] ?? ucfirst(str_replace('_', ' ', $this->section));
    }



    // Additional method for Filament to get page options
    public static function getPageOptions(): array
    {
        $pages = [];
        foreach (static::getPageStructure() as $key => $sections) {
            $pages[$key] = ucfirst(str_replace('_', ' ', $key));
        }
        return $pages;
    }

    // Additional method for Filament to get section options for a page
    public static function getSectionOptions(string $page = null): array
    {
        if (!$page) {
            return [];
        }

        return static::getSectionsForPage($page);
    }


   public function getContentPreviewAttribute(): string
    {
        $rawContent = $this->attributes['content'] ?? '';

        if ($this->type === 'json') {
            $decoded = json_decode($rawContent, true);
            if ($decoded === null) return 'Invalid JSON';

            if (is_array($decoded)) {
                $count = count($decoded);

                // Show first few keys for preview without showing JSON structure
                if (isset($decoded[0]) && is_array($decoded[0])) {
                    // Array of objects - show structure info
                    $firstItem = $decoded[0];
                    $keys = array_keys($firstItem);
                    $preview = implode(', ', array_slice($keys, 0, 3));
                    if (count($keys) > 3) $preview .= '...';
                    return "Structured data ({$count} items): {$preview}";
                } else {
                    // Simple key-value pairs
                    $keys = array_keys($decoded);
                    $preview = implode(', ', array_slice($keys, 0, 3));
                    if (count($keys) > 3) $preview .= '...';
                    return "Data ({$count} keys): {$preview}";
                }
            }

            return 'Configuration data';
        }

        if ($this->type === 'image') {
            return 'Image: ' . basename($rawContent);
        }

        if ($this->type === 'html') {
            return strip_tags($rawContent);
        }

        if ($this->type === 'boolean') {
            return filter_var($rawContent, FILTER_VALIDATE_BOOLEAN) ? 'Enabled' : 'Disabled';
        }

        if ($this->type === 'url') {
            return 'Link: ' . $rawContent;
        }

        return $rawContent;
    }

public function setContentAttribute($value)
{
    // Handle Filament FileUpload component output
    if ($this->type === 'image') {
        if (is_array($value)) {
            // If it's an array, take the first value (file path)
            $value = !empty($value) ? $value[0] : null;
        } elseif (is_string($value) && str_starts_with($value, '[')) {
            // Handle JSON array format
            $decoded = json_decode($value, true);
            if ($decoded && is_array($decoded) && !empty($decoded)) {
                $value = $decoded[0];
            }
        } elseif (is_string($value) && str_starts_with($value, '{')) {
            // Handle JSON object format with UUID
            $decoded = json_decode($value, true);
            if ($decoded && is_array($decoded)) {
                // Extract the file path from the object
                $value = array_values($decoded)[0] ?? null;
            }
        }
        // If value is still an array after all processing, take first element
        if (is_array($value) && !empty($value)) {
            $value = $value[0];
        }
    }

    $this->attributes['content'] = $value;
}

// Also add this method to handle the content accessor more carefully:
protected function content(): Attribute
{
    return Attribute::make(
        get: function ($value) {
            // Always return raw value for Filament admin to prevent conflicts
            if (app()->runningInConsole() ||
                request()->is('admin/*') ||
                request()->is('livewire/*') ||
                app('livewire')->isLivewireRequest()) {
                return $value;
            }

            // For frontend, return formatted content
            return match($this->type) {
                'json' => json_decode($value, true),
                'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
                'number' => is_numeric($value) ? (float) $value : $value,
                'image' => $this->getFormattedImageUrl($value),
                'url' => $value,
                default => $value
            };
        }
    );
}

// Add this accessor to your model for displaying images

private function getFormattedImageUrl($value): ?string
{
    if (!$value) {
        return null;
    }

    // Handle JSON object format (with UUID)
    if (is_string($value) && str_starts_with($value, '{')) {
        $decoded = json_decode($value, true);
        if ($decoded && is_array($decoded)) {
            $filePath = array_values($decoded)[0] ?? null;
            if ($filePath) {
                return str_starts_with($filePath, 'http') ? $filePath : asset('storage/' . $filePath);
            }
        }
    }

    // Handle JSON array format
    if (is_string($value) && str_starts_with($value, '[')) {
        $decoded = json_decode($value, true);
        if ($decoded && is_array($decoded) && !empty($decoded)) {
            $filePath = $decoded[0];
            return str_starts_with($filePath, 'http') ? $filePath : asset('storage/' . $filePath);
        }
    }

    // Handle direct file path
    return str_starts_with($value, 'http') ? $value : asset('storage/' . $value);
}

// Updated image URL accessor
public function getImageUrlAttribute(): ?string
{
    if ($this->type !== 'image' || !$this->getRawOriginal('content')) {
        return null;
    }

    return $this->getFormattedImageUrl($this->getRawOriginal('content'));
}

// Add a method to get the clean file path (without full URL)
public function getImagePathAttribute(): ?string
{
    if ($this->type !== 'image' || !$this->getRawOriginal('content')) {
        return null;
    }

    $content = $this->getRawOriginal('content');

    // Handle JSON object format (with UUID)
    if (is_string($content) && str_starts_with($content, '{')) {
        $decoded = json_decode($content, true);
        if ($decoded && is_array($decoded)) {
            return array_values($decoded)[0] ?? null;
        }
    }

    // Handle JSON array format
    if (is_string($content) && str_starts_with($content, '[')) {
        $decoded = json_decode($content, true);
        if ($decoded && is_array($decoded) && !empty($decoded)) {
            return $decoded[0];
        }
    }

    // Return direct path
    return $content;
}
}


