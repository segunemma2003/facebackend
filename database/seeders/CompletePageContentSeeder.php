<?php
// database/seeders/CompletePageContentSeeder.php

namespace Database\Seeders;

use App\Models\PageContent;
use Illuminate\Database\Seeder;

class CompletePageContentSeeder extends Seeder
{
    public function run()
    {
        $allContent = [
            // HOMEPAGE CONTENT
            'homepage' => [
                'hero' => [
                    [
                        'key' => 'main_title',
                        'type' => 'text',
                        'content' => 'Celebrating Global Excellence',
                        'sort_order' => 1
                    ],
                    [
                        'key' => 'main_subtitle',
                        'type' => 'html',
                        'content' => 'Recognizing outstanding achievements in <span class="font-semibold text-face-sky-blue-light">Focus</span>, <span class="font-semibold text-face-sky-blue-light">Achievement</span>, <span class="font-semibold text-face-sky-blue-light">Courage</span>, and <span class="font-semibold text-face-sky-blue-light">Excellence</span> across the world.',
                        'sort_order' => 2
                    ],
                    [
                        'key' => 'current_highlight_subtitle',
                        'type' => 'text',
                        'content' => '2025 Voting Now Open',
                        'sort_order' => 3
                    ],
                    [
                        'key' => 'current_highlight_content',
                        'type' => 'text',
                        'content' => 'Cast your vote for outstanding nominees across multiple categories representing innovation and excellence from around the world.',
                        'sort_order' => 4
                    ],
                    [
                        'key' => 'primary_button_text',
                        'type' => 'text',
                        'content' => 'View Current Nominees',
                        'sort_order' => 5
                    ],
                    [
                        'key' => 'secondary_button_text',
                        'type' => 'text',
                        'content' => 'Register for Event',
                        'sort_order' => 6
                    ]
                ],
                'about' => [
                    [
                        'key' => 'title',
                        'type' => 'text',
                        'content' => 'About FACE Awards',
                        'sort_order' => 1
                    ],
                    [
                        'key' => 'content',
                        'type' => 'html',
                        'content' => 'The Outstanding FACE Global Recognition Awards is an international platform created by Mr. Thompson Alade, a seasoned and professional leadership and tech management expert, to celebrate and honor individuals, organizations, and institutions making remarkable contributions across diverse sectors worldwide.',
                        'sort_order' => 2
                    ],
                    [
                        'key' => 'face_meanings',
                        'type' => 'json',
                        'content' => json_encode([
                            ['letter' => 'F', 'word' => 'Focus', 'description' => 'The unwavering commitment to vision and purpose'],
                            ['letter' => 'A', 'word' => 'Achievement', 'description' => 'Significant accomplishments and measurable success'],
                            ['letter' => 'C', 'word' => 'Courage', 'description' => 'The boldness to innovate and overcome challenges'],
                            ['letter' => 'E', 'word' => 'Excellence', 'description' => 'The pursuit of the highest standards in every endeavor']
                        ]),
                        'sort_order' => 3
                    ]
                ],
                'approach' => [
                    [
                        'key' => 'face_sub_title',
                        'type' => 'text',
                        'content' => 'FACE Represents',
                        'sort_order' => 1
                    ],
                    [
                        'key' => 'approach_title',
                        'type' => 'text',
                        'content' => 'Our Approach',
                        'sort_order' => 2
                    ],
                    [
                        'key' => 'approach_content',
                        'type' => 'html',
                        'content' => 'Our comprehensive approach combines global reach with local impact, ensuring that excellence is recognized across all sectors and communities.',
                        'sort_order' => 3
                    ],
                    [
                        'key' => 'image_title',
                        'type' => 'text',
                        'content' => 'Celebrating excellence across borders and industries',
                        'sort_order' => 4
                    ],
                    [
                        'key' => 'approach_items',
                        'type' => 'json',
                        'content' => json_encode([
                            [
                                'title' => 'Global Reach, Local Impact',
                                'description' => 'Recognizing excellence worldwide, from local heroes to global brands making waves across continents',
                                'icon' => 'globe'
                            ],
                            [
                                'title' => 'People-Centered Nomination',
                                'description' => 'Open, inclusive polling systems that ensure recognition comes directly from those who experience the impact',
                                'icon' => 'users'
                            ],
                            [
                                'title' => 'Personal Award Delivery',
                                'description' => 'Beautifully crafted trophies and plaques delivered securely or presented personally to honorees worldwide',
                                'icon' => 'trophy'
                            ],
                            [
                                'title' => 'End-of-Year Ceremony',
                                'description' => 'Optional annual grand recognition event for networking and celebration in an atmosphere of elegance',
                                'icon' => 'calendar'
                            ],
                            [
                                'title' => 'Diverse International Collaboration',
                                'description' => 'A multicultural team of professionals ensuring our work remains relevant and inclusive globally',
                                'icon' => 'handshake'
                            ]
                        ]),
                        'sort_order' => 5
                    ]
                ],
                'upcoming_categories' => [
                    [
                        'key' => 'subtitle',
                        'type' => 'text',
                        'content' => 'The FACE Awards recognize excellence across a diverse range of categories',
                        'sort_order' => 1
                    ],
                    [
                        'key' => 'content',
                        'type' => 'html',
                        'content' => 'Each category represents a vital area of human achievement and innovation, from technology and business to humanitarian work and cultural excellence.',
                        'sort_order' => 2
                    ]
                ],
                'award_ceremony' => [
                    [
                        'key' => 'title',
                        'type' => 'text',
                        'content' => 'Upcoming Award Ceremony',
                        'sort_order' => 1
                    ],
                    [
                        'key' => 'subtitle',
                        'type' => 'text',
                        'content' => '2025 FACE Global Awards Ceremony',
                        'sort_order' => 2
                    ],
                    [
                        'key' => 'content',
                        'type' => 'html',
                        'content' => 'Join us for a prestigious evening celebrating excellence and achievement. Register now to attend our next award ceremony.',
                        'sort_order' => 3
                    ],
                    [
                        'key' => 'event_date',
                        'type' => 'text',
                        'content' => 'December 15, 2024',
                        'sort_order' => 4
                    ],
                    [
                        'key' => 'venue',
                        'type' => 'text',
                        'content' => 'Grand Ballroom, The Prestigious Hotel, New York City',
                        'sort_order' => 5
                    ],
                    [
                        'key' => 'ticket_info',
                        'type' => 'json',
                        'content' => json_encode([
                            ['type' => 'Standard Attendance', 'price' => '$250', 'description' => 'General admission with dinner'],
                            ['type' => 'VIP Experience', 'price' => '$450', 'description' => 'Premium seating with cocktail reception'],
                            ['type' => 'Corporate Table (8 guests)', 'price' => '$1,800', 'description' => 'Reserved table for corporate sponsors']
                        ]),
                        'sort_order' => 6
                    ]
                ]
            ],

            // GLOBAL SETTINGS
            'global_settings' => [
                'social_media' => [
                    [
                        'key' => 'facebook_url',
                        'type' => 'url',
                        'content' => 'https://facebook.com/faceawards',
                        'sort_order' => 1
                    ],
                    [
                        'key' => 'twitter_url',
                        'type' => 'url',
                        'content' => 'https://twitter.com/faceawards',
                        'sort_order' => 2
                    ],
                    [
                        'key' => 'instagram_url',
                        'type' => 'url',
                        'content' => 'https://instagram.com/faceawards',
                        'sort_order' => 3
                    ],
                    [
                        'key' => 'linkedin_url',
                        'type' => 'url',
                        'content' => 'https://linkedin.com/company/faceawards',
                        'sort_order' => 4
                    ],
                    [
                        'key' => 'social_links_json',
                        'type' => 'json',
                        'content' => json_encode([
                            ['platform' => 'Facebook', 'url' => 'https://facebook.com/faceawards', 'icon' => 'facebook'],
                            ['platform' => 'Twitter', 'url' => 'https://twitter.com/faceawards', 'icon' => 'twitter'],
                            ['platform' => 'Instagram', 'url' => 'https://instagram.com/faceawards', 'icon' => 'instagram'],
                            ['platform' => 'LinkedIn', 'url' => 'https://linkedin.com/company/faceawards', 'icon' => 'linkedin']
                        ]),
                        'sort_order' => 5
                    ]
                ],
                'contact_info' => [
                    [
                        'key' => 'primary_email',
                        'type' => 'text',
                        'content' => 'info@faceawards.org',
                        'sort_order' => 1
                    ],
                    [
                        'key' => 'support_email',
                        'type' => 'text',
                        'content' => 'support@faceawards.org',
                        'sort_order' => 2
                    ],
                    [
                        'key' => 'address',
                        'type' => 'html',
                        'content' => '3120 Southwest Freeway 1st Floor<br>2003 Houston TX 77098',
                        'sort_order' => 3
                    ],
                    [
                        'key' => 'city',
                        'type' => 'text',
                        'content' => 'Houston',
                        'sort_order' => 4
                    ],
                    [
                        'key' => 'state',
                        'type' => 'text',
                        'content' => 'Texas',
                        'sort_order' => 5
                    ],
                    [
                        'key' => 'country',
                        'type' => 'text',
                        'content' => 'United States',
                        'sort_order' => 6
                    ],
                    [
                        'key' => 'postal_code',
                        'type' => 'text',
                        'content' => '77098',
                        'sort_order' => 7
                    ]
                ],
                'footer' => [
                    [
                        'key' => 'footer_note',
                        'type' => 'text',
                        'content' => 'Excellence Recognized Globally',
                        'sort_order' => 1
                    ],
                    [
                        'key' => 'copyright_text',
                        'type' => 'text',
                        'content' => 'Outstanding FACE Global Recognition Awards. All rights reserved.',
                        'sort_order' => 2
                    ]
                ],
                'company_info' => [
                    [
                        'key' => 'company_name',
                        'type' => 'text',
                        'content' => 'Outstanding FACE Global Recognition Awards',
                        'sort_order' => 1
                    ],
                    [
                        'key' => 'company_motto',
                        'type' => 'text',
                        'content' => 'Celebrating excellence across borders and industries',
                        'sort_order' => 2
                    ],
                    [
                        'key' => 'company_description',
                        'type' => 'html',
                        'content' => 'An international platform celebrating outstanding individuals and organizations making meaningful impact across the globe.',
                        'sort_order' => 3
                    ],
                    [
                        'key' => 'founded_year',
                        'type' => 'text',
                        'content' => '2024',
                        'sort_order' => 4
                    ]
                ]
            ]
        ];

        // Insert all content
        foreach ($allContent as $page => $sections) {
            foreach ($sections as $section => $items) {
                foreach ($items as $item) {
                    PageContent::updateOrCreate(
                        [
                            'page' => $page,
                            'section' => $section,
                            'key' => $item['key']
                        ],
                        array_merge($item, [
                            'page' => $page,
                            'section' => $section,
                            'is_active' => true
                        ])
                    );
                }
            }
        }

        $this->command->info('Complete page content seeded successfully!');
    }
}
