<?php

namespace Database\Seeders;

use App\Models\PageContent;
use Illuminate\Database\Seeder;

class SimplePageContentSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data first
        PageContent::truncate();

        $contents = [
            // Homepage content
            [
                'page' => 'homepage',
                'section' => 'hero',
                'key' => 'main_title',
                'type' => 'text',
                'content' => 'Welcome to FACE Awards 2025',
                'sort_order' => 1,
                'is_active' => true
            ],
            [
                'page' => 'homepage',
                'section' => 'hero',
                'key' => 'main_subtitle',
                'type' => 'text',
                'content' => 'Celebrating Excellence in Leadership',
                'sort_order' => 2,
                'is_active' => true
            ],
            [
                'page' => 'homepage',
                'section' => 'about',
                'key' => 'title',
                'type' => 'text',
                'content' => 'About FACE Awards',
                'sort_order' => 1,
                'is_active' => true
            ],

            // About page content
            [
                'page' => 'about',
                'section' => 'hero',
                'key' => 'title',
                'type' => 'text',
                'content' => 'About Us',
                'sort_order' => 1,
                'is_active' => true
            ],
            [
                'page' => 'about',
                'section' => 'story',
                'key' => 'content',
                'type' => 'html',
                'content' => '<p>Our story begins with a vision to recognize outstanding leaders.</p>',
                'sort_order' => 1,
                'is_active' => true
            ],

            // Approach page content
            [
                'page' => 'approach',
                'section' => 'hero',
                'key' => 'title',
                'type' => 'text',
                'content' => 'Our Approach',
                'sort_order' => 1,
                'is_active' => true
            ],
            [
                'page' => 'approach',
                'section' => 'methodology',
                'key' => 'content',
                'type' => 'html',
                'content' => '<p>Our methodology is based on proven principles and best practices.</p>',
                'sort_order' => 1,
                'is_active' => true
            ],

            // Categories page content
            [
                'page' => 'categories',
                'section' => 'hero',
                'key' => 'title',
                'type' => 'text',
                'content' => 'Award Categories',
                'sort_order' => 1,
                'is_active' => true
            ],

            // Contact page content
            [
                'page' => 'contact',
                'section' => 'hero',
                'key' => 'title',
                'type' => 'text',
                'content' => 'Contact Us',
                'sort_order' => 1,
                'is_active' => true
            ],

            // Global settings
            [
                'page' => 'global_settings',
                'section' => 'social_media',
                'key' => 'facebook_url',
                'type' => 'url',
                'content' => 'https://facebook.com/faceawards',
                'sort_order' => 1,
                'is_active' => true
            ]
        ];

        foreach ($contents as $content) {
            PageContent::create($content);
        }

        $this->command->info('Created ' . count($contents) . ' PageContent records');
    }
}
