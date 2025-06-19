<?php
// database/seeders/EnhancedPageContentSeeder.php

namespace Database\Seeders;

use App\Models\PageContent;
use Illuminate\Database\Seeder;

class EnhancedPageContentSeeder extends Seeder
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
                        'content' => "Celebrating\nGlobal Excellence",
                        'sort_order' => 1
                    ],
                    [
                        'key' => 'subtitle',
                        'type' => 'html',
                        'content' => 'Recognizing outstanding achievements in <span class="font-semibold text-face-sky-blue-light">Focus</span>, <span class="font-semibold text-face-sky-blue-light">Achievement</span>, <span class="font-semibold text-face-sky-blue-light">Courage</span>, and <span class="font-semibold text-face-sky-blue-light">Excellence</span> across the world.',
                        'sort_order' => 2
                    ],
                    [
                        'key' => 'primary_button_text',
                        'type' => 'text',
                        'content' => 'View Current Nominees',
                        'sort_order' => 3
                    ],
                    [
                        'key' => 'secondary_button_text',
                        'type' => 'text',
                        'content' => 'Register for Event',
                        'sort_order' => 4
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
                        'key' => 'description',
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
                ]
            ],

            // NOMINEES PAGE CONTENT
            'nominees' => [
                'hero' => [
                    [
                        'key' => 'title',
                        'type' => 'text',
                        'content' => 'Current Nominees',
                        'sort_order' => 1
                    ],
                    [
                        'key' => 'subtitle',
                        'type' => 'text',
                        'content' => 'Meet the outstanding individuals and organizations nominated for the 2025 FACE Awards',
                        'sort_order' => 2
                    ]
                ],
                'voting_info' => [
                    [
                        'key' => 'voting_instructions',
                        'type' => 'html',
                        'content' => '<p>Voting is open to the public. You can vote once per category. Voting ends on <strong>December 31, 2024</strong>.</p>',
                        'sort_order' => 1
                    ]
                ]
            ],

            // CATEGORIES PAGE CONTENT
            'categories' => [
                'hero' => [
                    [
                        'key' => 'title',
                        'type' => 'text',
                        'content' => 'Award Categories',
                        'sort_order' => 1
                    ],
                    [
                        'key' => 'subtitle',
                        'type' => 'text',
                        'content' => 'Explore our diverse range of award categories recognizing excellence across multiple sectors',
                        'sort_order' => 2
                    ]
                ],
                'introduction' => [
                    [
                        'key' => 'content',
                        'type' => 'html',
                        'content' => '<p>Our award categories span across technology, business, humanitarian work, education, sustainability, and cultural excellence. Each category has specific criteria designed to recognize true achievement and impact.</p>',
                        'sort_order' => 1
                    ]
                ]
            ],

            // PAST WINNERS PAGE CONTENT
            'past_winners' => [
                'hero' => [
                    [
                        'key' => 'title',
                        'type' => 'text',
                        'content' => 'Past Winners',
                        'sort_order' => 1
                    ],
                    [
                        'key' => 'subtitle',
                        'type' => 'text',
                        'content' => 'Celebrating the remarkable individuals and organizations who have previously received FACE Awards',
                        'sort_order' => 2
                    ]
                ]
            ],

            // GALLERY PAGE CONTENT
            'gallery' => [
                'hero' => [
                    [
                        'key' => 'title',
                        'type' => 'text',
                        'content' => 'Event Gallery',
                        'sort_order' => 1
                    ],
                    [
                        'key' => 'subtitle',
                        'type' => 'text',
                        'content' => 'Moments from our award ceremonies and events celebrating global excellence',
                        'sort_order' => 2
                    ]
                ]
            ],

            // APPROACH PAGE CONTENT
            'approach' => [
                'hero' => [
                    [
                        'key' => 'title',
                        'type' => 'text',
                        'content' => 'Our Approach',
                        'sort_order' => 1
                    ],
                    [
                        'key' => 'subtitle',
                        'type' => 'text',
                        'content' => 'How we identify, evaluate, and recognize outstanding achievements worldwide',
                        'sort_order' => 2
                    ]
                ],
                'methodology' => [
                    [
                        'key' => 'title',
                        'type' => 'text',
                        'content' => 'Our Methodology',
                        'sort_order' => 1
                    ],
                    [
                        'key' => 'content',
                        'type' => 'html',
                        'content' => '<p>Our rigorous evaluation process combines expert panel reviews, public voting, and impact assessment to ensure we recognize truly deserving candidates.</p>',
                        'sort_order' => 2
                    ]
                ]
            ],

            // ABOUT PAGE CONTENT
            'about' => [
                'hero' => [
                    [
                        'key' => 'title',
                        'type' => 'text',
                        'content' => 'About FACE Awards',
                        'sort_order' => 1
                    ],
                    [
                        'key' => 'subtitle',
                        'type' => 'text',
                        'content' => 'Learn more about our mission, vision, and the people behind the FACE Awards',
                        'sort_order' => 2
                    ]
                ],
                'story' => [
                    [
                        'key' => 'title',
                        'type' => 'text',
                        'content' => 'Our Story',
                        'sort_order' => 1
                    ],
                    [
                        'key' => 'content',
                        'type' => 'html',
                        'content' => '<p>The FACE Awards was founded with a vision to create a global platform for recognizing excellence across diverse fields and cultures...</p>',
                        'sort_order' => 2
                    ]
                ]
            ],

            // CONTACT PAGE CONTENT
            'contact' => [
                'hero' => [
                    [
                        'key' => 'title',
                        'type' => 'text',
                        'content' => 'Contact Us',
                        'sort_order' => 1
                    ],
                    [
                        'key' => 'subtitle',
                        'type' => 'text',
                        'content' => 'Get in touch with our team for inquiries, nominations, or partnership opportunities',
                        'sort_order' => 2
                    ]
                ],
                'contact_info' => [
                    [
                        'key' => 'address',
                        'type' => 'html',
                        'content' => '<p>3120 Southwest Freeway 1st Floor<br>2003 Houston TX 77098</p>',
                        'sort_order' => 1
                    ],
                    [
                        'key' => 'email',
                        'type' => 'text',
                        'content' => 'info@faceawards.org',
                        'sort_order' => 2
                    ]
                ]
            ],

            // FOOTER CONTENT
            'footer' => [
                'contact' => [
                    [
                        'key' => 'address',
                        'type' => 'html',
                        'content' => '3120 Southwest Freeway 1st Floor<br>2003 Houston TX 77098',
                        'sort_order' => 1
                    ],
                    [
                        'key' => 'email',
                        'type' => 'text',
                        'content' => 'info@faceawards.org',
                        'sort_order' => 2
                    ]
                ],
                'social' => [
                    [
                        'key' => 'social_links',
                        'type' => 'json',
                        'content' => json_encode([
                            ['platform' => 'Facebook', 'url' => '#', 'icon' => 'facebook'],
                            ['platform' => 'Twitter', 'url' => '#', 'icon' => 'twitter'],
                            ['platform' => 'Instagram', 'url' => '#', 'icon' => 'instagram'],
                            ['platform' => 'LinkedIn', 'url' => '#', 'icon' => 'linkedin']
                        ]),
                        'sort_order' => 1
                    ]
                ],
                'copyright' => [
                    [
                        'key' => 'copyright_text',
                        'type' => 'text',
                        'content' => 'Outstanding FACE Global Recognition Awards. All rights reserved.',
                        'sort_order' => 1
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

        $this->command->info('Enhanced page content seeded successfully!');
    }
}
