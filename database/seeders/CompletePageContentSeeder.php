<?php
// database/seeders/CompletePageContentSeeder.php

namespace Database\Seeders;

use App\Models\PageContent;
use Illuminate\Database\Seeder;

class CompletePageContentSeeder extends Seeder
{
    public function run()
    {
          PageContent::truncate();
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
                'trending_ticker' => [
                    [
                        'key' => 'enabled',
                        'type' => 'boolean',
                        'content' => 'true',
                        'sort_order' => 1
                    ],
                    [
                        'key' => 'ticker_label',
                        'type' => 'text',
                        'content' => 'Now Trending:',
                        'sort_order' => 2
                    ],
                    [
                        'key' => 'fallback_message',
                        'type' => 'text',
                        'content' => 'Check out our latest nominees and vote for excellence!',
                        'sort_order' => 3
                    ],
                    [
                        'key' => 'auto_rotate_speed',
                        'type' => 'number',
                        'content' => '4000',
                        'sort_order' => 4
                    ],
                    [
                        'key' => 'show_vote_counts',
                        'type' => 'boolean',
                        'content' => 'true',
                        'sort_order' => 5
                    ],
                    [
                        'key' => 'show_percentages',
                        'type' => 'boolean',
                        'content' => 'true',
                        'sort_order' => 6
                    ],
                    [
                        'key' => 'background_color',
                        'type' => 'text',
                        'content' => 'bg-face-sky-blue/90',
                        'sort_order' => 7
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
                    ],
                    // ADD IMAGE FIELDS
                    [
                        'key' => 'hero_image',
                        'type' => 'image',
                        'content' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?q=80&w=1470&auto=format&fit=crop',
                        'meta' => json_encode(['alt' => 'FACE Awards Ceremony', 'caption' => 'Celebrating excellence across borders and industries']),
                        'sort_order' => 4
                    ],
                    [
                        'key' => 'hero_image_fallback',
                        'type' => 'image',
                        'content' => 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?q=80&w=1470&auto=format&fit=crop',
                        'meta' => json_encode(['alt' => 'Awards ceremony fallback', 'caption' => 'Excellence recognized globally']),
                        'sort_order' => 5
                    ],
                    [
                        'key' => 'image_caption',
                        'type' => 'text',
                        'content' => 'Celebrating excellence across borders and industries',
                        'sort_order' => 6
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
                        'key' => 'title',
                        'type' => 'text',
                        'content' => 'Award Categories',
                        'sort_order' => 1
                    ],
                    [
                        'key' => 'subtitle',
                        'type' => 'text',
                        'content' => 'The FACE Awards recognize excellence across a diverse range of categories',
                        'sort_order' => 2
                    ],
                    [
                        'key' => 'content',
                        'type' => 'html',
                        'content' => 'Each category represents a vital area of human achievement and innovation, from technology and business to humanitarian work and cultural excellence.',
                        'sort_order' => 3
                    ]
                ],
                'gallery_section' => [
                    [
                        'key' => 'title',
                        'type' => 'text',
                        'content' => 'Event Gallery',
                        'sort_order' => 1
                    ],
                    [
                        'key' => 'subtitle',
                        'type' => 'text',
                        'content' => 'Moments from Our Ceremonies',
                        'sort_order' => 2
                    ],
                    [
                        'key' => 'content',
                        'type' => 'html',
                        'content' => 'Explore moments from our past ceremonies and events that celebrate excellence and achievement across the globe.',
                        'sort_order' => 3
                    ],
                    [
                        'key' => 'empty_state_message',
                        'type' => 'text',
                        'content' => 'Gallery events will be available soon. Check back later for photos from our ceremonies and events.',
                        'sort_order' => 4
                    ],
                    [
                        'key' => 'button_text',
                        'type' => 'text',
                        'content' => 'View Complete Gallery',
                        'sort_order' => 5
                    ]
                ],
                'past_winners' => [
                    [
                        'key' => 'title',
                        'type' => 'text',
                        'content' => 'Past Winners',
                        'sort_order' => 1
                    ],
                    [
                        'key' => 'subtitle',
                        'type' => 'text',
                        'content' => 'Celebrating Excellence Through the Years',
                        'sort_order' => 2
                    ],
                    [
                        'key' => 'content',
                        'type' => 'html',
                        'content' => 'Celebrating the remarkable individuals and organizations who have previously received the FACE Awards for their outstanding contributions.',
                        'sort_order' => 3
                    ],
                    [
                        'key' => 'empty_state_message',
                        'type' => 'text',
                        'content' => 'Past winners will be featured here after our first awards ceremony.',
                        'sort_order' => 4
                    ],
                    [
                        'key' => 'button_text',
                        'type' => 'text',
                        'content' => 'View All Past Winners',
                        'sort_order' => 5
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
                        'key' => 'description',
                        'type' => 'html',
                        'content' => 'An evening of celebration, networking and recognition for industry leaders, nominees, and award recipients.',
                        'sort_order' => 6
                    ],
                    [
                        'key' => 'dress_code',
                        'type' => 'text',
                        'content' => 'Black Tie Event',
                        'sort_order' => 7
                    ],
                    [
                        'key' => 'expected_attendance',
                        'type' => 'text',
                        'content' => 'Expected Attendance: 500+ guests',
                        'sort_order' => 8
                    ],
                    [
                        'key' => 'registration_open_message',
                        'type' => 'text',
                        'content' => 'Register now to attend our next award ceremony.',
                        'sort_order' => 9
                    ],
                    [
                        'key' => 'registration_closed_message',
                        'type' => 'text',
                        'content' => 'Registration will open soon.',
                        'sort_order' => 10
                    ],
                    [
                        'key' => 'registration_button_text',
                        'type' => 'text',
                        'content' => 'Complete Registration',
                        'sort_order' => 11
                    ],
                    [
                        'key' => 'ticket_info',
                        'type' => 'json',
                        'content' => json_encode([
                            ['type' => 'Standard Attendance', 'price' => '$250', 'description' => 'General admission with dinner'],
                            ['type' => 'VIP Experience', 'price' => '$450', 'description' => 'Premium seating with cocktail reception'],
                            ['type' => 'Corporate Table (8 guests)', 'price' => '$1,800', 'description' => 'Reserved table for corporate sponsors']
                        ]),
                        'sort_order' => 12
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
                        'key' => 'nominations_email',
                        'type' => 'text',
                        'content' => 'nominations@faceawards.org',
                        'sort_order' => 3
                    ],
                    [
                        'key' => 'phone_international',
                        'type' => 'text',
                        'content' => '+1 (234) 567-8901',
                        'sort_order' => 4
                    ],
                    [
                        'key' => 'phone_toll_free',
                        'type' => 'text',
                        'content' => '1-800-555-1000',
                        'sort_order' => 5
                    ],
                    [
                        'key' => 'address',
                        'type' => 'html',
                        'content' => '3120 Southwest Freeway 1st Floor<br>2003 Houston TX 77098',
                        'sort_order' => 6
                    ],
                    [
                        'key' => 'full_address',
                        'type' => 'html',
                        'content' => 'FACE Awards Global Headquarters<br>3120 Southwest freeway 1st floor<br>2003 Houston TX 77098<br>United States',
                        'sort_order' => 7
                    ],
                    [
                        'key' => 'city',
                        'type' => 'text',
                        'content' => 'Houston',
                        'sort_order' => 8
                    ],
                    [
                        'key' => 'state',
                        'type' => 'text',
                        'content' => 'Texas',
                        'sort_order' => 9
                    ],
                    [
                        'key' => 'country',
                        'type' => 'text',
                        'content' => 'United States',
                        'sort_order' => 10
                    ],
                    [
                        'key' => 'postal_code',
                        'type' => 'text',
                        'content' => '77098',
                        'sort_order' => 11
                    ],
                    [
                        'key' => 'office_hours',
                        'type' => 'html',
                        'content' => 'Monday - Friday: 9:00 AM - 5:00 PM (EST)<br>Saturday & Sunday: Closed',
                        'sort_order' => 12
                    ],
                    [
                        'key' => 'response_time',
                        'type' => 'text',
                        'content' => 'Within 24-48 business hours',
                        'sort_order' => 13
                    ],
                    [
                        'key' => 'google_maps_embed_url',
                        'type' => 'url',
                        'content' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d387193.3059353029!2d-74.25986548248684!3d40.697149422113014!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew%20York%2C%20NY%2C%20USA!5e0!3m2!1sen!2sca!4v1653486359204!5m2!1sen!2sca',
                        'sort_order' => 14
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
                    ],

        // NOMINEES PAGE CONTENT
        'nominees' => [
            'hero' => [
                [
                    'key' => 'main_title',
                    'type' => 'text',
                    'content' => 'Current Nominees',
                    'sort_order' => 1
                ],
                [
                    'key' => 'main_subtitle',
                    'type' => 'text',
                    'content' => 'Vote for outstanding individuals and organizations making remarkable contributions across various sectors worldwide',
                    'sort_order' => 2
                ],
                [
                    'key' => 'background_image',
                    'type' => 'image',
                    'content' => 'https://images.pexels.com/photos/1181298/pexels-photo-1181298.jpeg?auto=compress&cs=tinysrgb&w=1920&h=1080&fit=crop',
                    'meta' => json_encode(['alt' => 'FACE Awards nominees background']),
                    'sort_order' => 3
                ]
            ],
            'countdown_timer' => [
                [
                    'key' => 'enabled',
                    'type' => 'boolean',
                    'content' => 'true',
                    'sort_order' => 1
                ],
                [
                    'key' => 'title',
                    'type' => 'text',
                    'content' => 'Voting Ends In',
                    'sort_order' => 2
                ],
                [
                    'key' => 'subtitle',
                    'type' => 'text',
                    'content' => 'Make your voice heard before time runs out!',
                    'sort_order' => 3
                ],
                [
                    'key' => 'voting_end_date_offset_days',
                    'type' => 'number',
                    'content' => '30',
                    'sort_order' => 4
                ]
            ],
            'category_filter' => [
                [
                    'key' => 'title',
                    'type' => 'text',
                    'content' => 'Filter by Category',
                    'sort_order' => 1
                ],
                [
                    'key' => 'subtitle',
                    'type' => 'text',
                    'content' => 'Explore nominees across different categories of excellence',
                    'sort_order' => 2
                ],
                [
                    'key' => 'all_categories_text',
                    'type' => 'text',
                    'content' => 'All Categories',
                    'sort_order' => 3
                ]
            ],
            'nominees_grid' => [
                [
                    'key' => 'title',
                    'type' => 'html',
                    'content' => 'Exceptional <span class="text-face-sky-blue">Nominees</span>',
                    'sort_order' => 1
                ],
                [
                    'key' => 'subtitle',
                    'type' => 'text',
                    'content' => 'Discover remarkable individuals and organizations competing for recognition',
                    'sort_order' => 2
                ],
                [
                    'key' => 'empty_state_title',
                    'type' => 'text',
                    'content' => 'No Nominees Found',
                    'sort_order' => 3
                ],
                [
                    'key' => 'empty_state_message',
                    'type' => 'text',
                    'content' => 'No nominees found for the selected category. Try selecting a different category.',
                    'sort_order' => 4
                ]
            ],
            'voting_actions' => [
                [
                    'key' => 'vote_button_text',
                    'type' => 'text',
                    'content' => 'Vote Now',
                    'sort_order' => 1
                ],
                [
                    'key' => 'voted_button_text',
                    'type' => 'text',
                    'content' => 'Voted',
                    'sort_order' => 2
                ],
                [
                    'key' => 'voting_text',
                    'type' => 'text',
                    'content' => 'Voting...',
                    'sort_order' => 3
                ],
                [
                    'key' => 'voting_closed_text',
                    'type' => 'text',
                    'content' => 'Voting Closed',
                    'sort_order' => 4
                ],
                [
                    'key' => 'already_voted_title',
                    'type' => 'text',
                    'content' => 'Already voted',
                    'sort_order' => 5
                ],
                [
                    'key' => 'already_voted_message',
                    'type' => 'text',
                    'content' => 'You have already voted for this nominee.',
                    'sort_order' => 6
                ],
                [
                    'key' => 'vote_success_title',
                    'type' => 'text',
                    'content' => 'Vote recorded!',
                    'sort_order' => 7
                ],
                [
                    'key' => 'vote_success_message',
                    'type' => 'text',
                    'content' => 'Thank you for your vote. It has been successfully recorded.',
                    'sort_order' => 8
                ],
                [
                    'key' => 'vote_failed_title',
                    'type' => 'text',
                    'content' => 'Vote failed',
                    'sort_order' => 9
                ],
                [
                    'key' => 'vote_failed_message',
                    'type' => 'text',
                    'content' => 'Failed to record vote. Please try again.',
                    'sort_order' => 10
                ]
            ],
            'call_to_action' => [
                [
                    'key' => 'title',
                    'type' => 'text',
                    'content' => 'Your Vote Matters',
                    'sort_order' => 1
                ],
                [
                    'key' => 'subtitle',
                    'type' => 'text',
                    'content' => 'Join thousands of voters worldwide in recognizing excellence and making a difference',
                    'sort_order' => 2
                ],
                [
                    'key' => 'primary_button_text',
                    'type' => 'text',
                    'content' => 'Watch Nominee Stories',
                    'sort_order' => 3
                ],
                [
                    'key' => 'secondary_button_text',
                    'type' => 'text',
                    'content' => 'Learn About FACE Awards',
                    'sort_order' => 4
                ]
            ],
            'loading_states' => [
                [
                    'key' => 'loading_nominees_text',
                    'type' => 'text',
                    'content' => 'Loading nominees...',
                    'sort_order' => 1
                ],
                [
                    'key' => 'failed_to_load_text',
                    'type' => 'text',
                    'content' => 'Failed to load nominees',
                    'sort_order' => 2
                ],
                [
                    'key' => 'try_again_button_text',
                    'type' => 'text',
                    'content' => 'Try Again',
                    'sort_order' => 3
                ]
            ],
            'stats_labels' => [
                [
                    'key' => 'active_nominees_label',
                    'type' => 'text',
                    'content' => 'Active Nominees',
                    'sort_order' => 1
                ],
                [
                    'key' => 'categories_open_label',
                    'type' => 'text',
                    'content' => 'Categories Open',
                    'sort_order' => 2
                ],
                [
                    'key' => 'total_votes_label',
                    'type' => 'text',
                    'content' => 'Total Votes',
                    'sort_order' => 3
                ],
                [
                    'key' => 'votes_suffix',
                    'type' => 'text',
                    'content' => 'votes',
                    'sort_order' => 4
                ]
            ]
        ],

        // ABOUT PAGE CONTENT
        'about' => [
            'hero' => [
                [
                    'key' => 'main_title',
                    'type' => 'html',
                    'content' => 'About the <span class="text-white">FACE Awards</span>',
                    'sort_order' => 1
                ],
                [
                    'key' => 'subtitle',
                    'type' => 'text',
                    'content' => 'Celebrating Focus, Achievement, Courage, and Excellence across the globe since 2010',
                    'sort_order' => 2
                ],
                [
                    'key' => 'background_image',
                    'type' => 'image',
                    'content' => 'https://images.pexels.com/photos/1181298/pexels-photo-1181298.jpeg?auto=compress&cs=tinysrgb&w=1920&h=1080&fit=crop',
                    'meta' => json_encode(['alt' => 'FACE Awards ceremony background']),
                    'sort_order' => 3
                ],
                [
                    'key' => 'stats',
                    'type' => 'json',
                    'content' => json_encode([
                        ['icon' => 'trophy', 'value' => '240+', 'label' => 'Recipients'],
                        ['icon' => 'users', 'value' => '50+', 'label' => 'Countries'],
                        ['icon' => 'star', 'value' => '12+', 'label' => 'Categories']
                    ]),
                    'sort_order' => 4
                ]
            ],
            'our_story' => [
                [
                    'key' => 'title',
                    'type' => 'html',
                    'content' => 'Our <span class="text-face-sky-blue">Story</span>',
                    'sort_order' => 1
                ],
                [
                    'key' => 'subtitle',
                    'type' => 'text',
                    'content' => 'The journey of recognizing excellence across borders and industries',
                    'sort_order' => 2
                ],
                [
                    'key' => 'content_paragraphs',
                    'type' => 'json',
                    'content' => json_encode([
                        'The Outstanding FACE Global Recognition Awards was established in 2010 by Mr. Thompson Alade, a seasoned leadership and technology management expert with a passion for celebrating excellence in all its forms. After witnessing remarkable achievements go unrecognized across various sectors and regions, he founded the FACE Awards to create a truly global platform for acknowledging outstanding contributions and impact.',
                        'What began as a small initiative has grown into an internationally recognized awards program that has honored over 240 recipients from 50 countries across 12 diverse categories. The name "FACE" represents the core values we seek to celebrate: Focus, Achievement, Courage, and Excellence.',
                        'Unlike many traditional awards programs that are limited by geography or industry, the FACE Awards maintains a uniquely global and democratic approach. We believe that excellence can be found everywhere—from bustling urban centers to remote rural communities, from established corporations to grassroots initiatives.',
                        'Our nomination and selection process ensures that recognition comes directly from those who experience the impact of our nominees\' work. Through this people-centered approach, we\'ve discovered and celebrated remarkable individuals and organizations that might otherwise have remained in the shadows, bringing their inspiring stories to a global audience.',
                        'As we continue to grow, we remain committed to our founding principles of inclusivity, fairness, and global representation. Every year, we expand our reach to new regions and sectors, discover inspiring stories of impact, and bring together a diverse community of excellence from around the world.'
                    ]),
                    'sort_order' => 3
                ],
                [
                    'key' => 'mission_title',
                    'type' => 'text',
                    'content' => 'Our Mission',
                    'sort_order' => 4
                ],
                [
                    'key' => 'mission_content',
                    'type' => 'text',
                    'content' => 'To discover, celebrate, and promote outstanding examples of Focus, Achievement, Courage, and Excellence across all sectors and regions of the world, inspiring a global culture of excellence and positive impact.',
                    'sort_order' => 5
                ],
                [
                    'key' => 'vision_title',
                    'type' => 'text',
                    'content' => 'Our Vision',
                    'sort_order' => 6
                ],
                [
                    'key' => 'vision_content',
                    'type' => 'text',
                    'content' => 'A world where exceptional contributions to human progress are recognized regardless of geography, background, or resources—where excellence is celebrated, shared, and inspires others to create positive change.',
                    'sort_order' => 7
                ]
            ],
            'success_stories' => [
                [
                    'key' => 'title',
                    'type' => 'html',
                    'content' => 'Success <span class="text-face-sky-blue">Stories</span>',
                    'sort_order' => 1
                ],
                [
                    'key' => 'subtitle',
                    'type' => 'text',
                    'content' => 'Discover how FACE Award recognition has amplified impact and opened new opportunities for our recipients',
                    'sort_order' => 2
                ],
                [
                    'key' => 'stories',
                    'type' => 'json',
                    'content' => json_encode([
                        [
                            'title' => 'EcoTech Solutions',
                            'award' => 'Technology Innovation Award, 2022',
                            'description' => 'After winning the FACE Award, this small sustainability startup secured $2 million in funding and expanded their water purification technology to 5 new countries, impacting over 100,000 lives.',
                            'image' => 'https://images.pexels.com/photos/356043/pexels-photo-356043.jpeg?auto=compress&cs=tinysrgb&w=800&h=400&fit=crop',
                            'alt' => 'EcoTech Solutions Success Story'
                        ],
                        [
                            'title' => 'Dr. Kwame Nkosi',
                            'award' => 'Humanitarian Impact Award, 2023',
                            'description' => 'Recognition from FACE Awards helped Dr. Nkosi\'s medical outreach program gain international attention, leading to partnerships with 3 major health organizations and expanded services to 12 remote communities.',
                            'image' => 'https://images.pexels.com/photos/5327585/pexels-photo-5327585.jpeg?auto=compress&cs=tinysrgb&w=800&h=400&fit=crop',
                            'alt' => 'Dr. Kwame Nkosi Success Story'
                        ],
                        [
                            'title' => 'Global Heritage Foundation',
                            'award' => 'Cultural Excellence Award, 2023',
                            'description' => 'Following their FACE Award win, this foundation\'s cultural preservation project received government support in 3 countries and established an international mentorship program reaching 500 young cultural ambassadors.',
                            'image' => 'https://images.pexels.com/photos/1181467/pexels-photo-1181467.jpeg?auto=compress&cs=tinysrgb&w=800&h=400&fit=crop',
                            'alt' => 'Global Heritage Foundation Success Story'
                        ]
                    ]),
                    'sort_order' => 3
                ],
                [
                    'key' => 'view_all_button_text',
                    'type' => 'text',
                    'content' => 'View All Success Stories',
                    'sort_order' => 4
                ],
                [
                    'key' => 'read_story_button_text',
                    'type' => 'text',
                    'content' => 'Read their story',
                    'sort_order' => 5
                ]
            ],
            'team' => [
                [
                    'key' => 'title',
                    'type' => 'html',
                    'content' => 'Our <span class="text-face-sky-blue">Team</span>',
                    'sort_order' => 1
                ],
                [
                    'key' => 'subtitle',
                    'type' => 'text',
                    'content' => 'Meet the diverse international team that makes the FACE Awards possible',
                    'sort_order' => 2
                ],
                [
                    'key' => 'core_team',
                    'type' => 'json',
                    'content' => json_encode([
                        [
                            'name' => 'Thompson Alade',
                            'title' => 'Founder & Chairman',
                            'description' => 'Leadership expert with 20+ years experience in tech management and global initiatives.',
                            'image' => 'https://images.unsplash.com/photo-1522529599102-193c0d76b5b6?w=800&q=80',
                            'alt' => 'Thompson Alade'
                        ],
                        [
                            'name' => 'Dr. Elena Marquez',
                            'title' => 'Global Partnerships Director',
                            'description' => 'International relations specialist connecting FACE Awards across 4 continents.',
                            'image' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=800&q=80',
                            'alt' => 'Dr. Elena Marquez'
                        ],
                        [
                            'name' => 'Jamal Ibrahim',
                            'title' => 'Awards Evaluation Lead',
                            'description' => 'Former academic with expertise in developing objective evaluation frameworks across cultures.',
                            'image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=800&q=80',
                            'alt' => 'Jamal Ibrahim'
                        ],
                        [
                            'name' => 'Sarah Okonjo',
                            'title' => 'Community Engagement Manager',
                            'description' => 'Social media expert connecting nominees and winners in a global community of excellence.',
                            'image' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=800&q=80',
                            'alt' => 'Sarah Okonjo'
                        ],
                        [
                            'name' => 'Michael Chen',
                            'title' => 'Technology & Innovation Director',
                            'description' => 'Software architect and digital strategist leading the platform development and innovation initiatives.',
                            'image' => 'https://images.unsplash.com/photo-1556157382-97eda2d62296?w=800&q=80',
                            'alt' => 'Michael Chen'
                        ],
                        [
                            'name' => 'Fatima Al-Rashid',
                            'title' => 'Regional Coordinator - Middle East',
                            'description' => 'Cultural liaison and program coordinator specializing in Middle Eastern partnerships and outreach.',
                            'image' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=800&q=80',
                            'alt' => 'Fatima Al-Rashid'
                        ],
                        [
                            'name' => 'David Thompson',
                            'title' => 'Communications & Media Director',
                            'description' => 'Former journalist with expertise in global communications and storytelling for social impact.',
                            'image' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=800&q=80',
                            'alt' => 'David Thompson'
                        ],
                        [
                            'name' => 'Dr. Priya Sharma',
                            'title' => 'Research & Analytics Lead',
                            'description' => 'Data scientist and researcher developing impact measurement frameworks and evaluation metrics.',
                            'image' => 'https://images.unsplash.com/photo-1582750433449-648ed127bb54?w=800&q=80',
                            'alt' => 'Dr. Priya Sharma'
                        ]
                    ]),
                    'sort_order' => 3
                ],
                [
                    'key' => 'advisory_board_title',
                    'type' => 'text',
                    'content' => 'International Advisory Board',
                    'sort_order' => 4
                ],
                [
                    'key' => 'advisory_board',
                    'type' => 'json',
                    'content' => json_encode([
                        [
                            'name' => 'Dr. Cheng Wei',
                            'region' => 'Asia-Pacific Region',
                            'expertise' => 'Technology Innovation Expert',
                            'image' => 'https://images.unsplash.com/photo-1566492031773-4f4e44671857?w=400&q=80',
                            'alt' => 'Dr. Cheng Wei'
                        ],
                        [
                            'name' => 'Amara Diallo',
                            'region' => 'Africa Region',
                            'expertise' => 'Sustainable Development Specialist',
                            'image' => 'https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?w=400&q=80',
                            'alt' => 'Amara Diallo'
                        ],
                        [
                            'name' => 'Carlos Mendoza',
                            'region' => 'Americas Region',
                            'expertise' => 'Business & Leadership Expert',
                            'image' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&q=80',
                            'alt' => 'Carlos Mendoza'
                        ],
                        [
                            'name' => 'Dr. Anna Schmidt',
                            'region' => 'Europe Region',
                            'expertise' => 'Education & Cultural Affairs Expert',
                            'image' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=400&q=80',
                            'alt' => 'Dr. Anna Schmidt'
                        ],
                        [
                            'name' => 'Dr. Raj Patel',
                            'region' => 'South Asia Region',
                            'expertise' => 'Healthcare Innovation & Social Impact',
                            'image' => 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?w=400&q=80',
                            'alt' => 'Dr. Raj Patel'
                        ],
                        [
                            'name' => 'Maria Santos',
                            'region' => 'Latin America Region',
                            'expertise' => 'Environmental Policy & Climate Action',
                            'image' => 'https://images.unsplash.com/photo-1494790108755-2616b332b1cb?w=400&q=80',
                            'alt' => 'Maria Santos'
                        ],
                        [
                            'name' => 'Prof. James Mitchell',
                            'region' => 'North America Region',
                            'expertise' => 'Academic Excellence & Research',
                            'image' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=400&q=80',
                            'alt' => 'Prof. James Mitchell'
                        ],
                        [
                            'name' => 'Dr. Fatou Keita',
                            'region' => 'West Africa Region',
                            'expertise' => 'Women\'s Empowerment & Community Development',
                            'image' => 'https://images.unsplash.com/photo-1531123897727-8f129e1688ce?w=400&q=80',
                            'alt' => 'Dr. Fatou Keita'
                        ],
                        [
                            'name' => 'Ahmed Hassan',
                            'region' => 'Middle East & North Africa',
                            'expertise' => 'Entrepreneurship & Innovation Ecosystems',
                            'image' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=400&q=80',
                            'alt' => 'Ahmed Hassan'
                        ],
                        [
                            'name' => 'Dr. Linda Nakamura',
                            'region' => 'East Asia Region',
                            'expertise' => 'Digital Transformation & AI Ethics',
                            'image' => 'https://images.unsplash.com/photo-1551836022-d5d88e9218df?w=400&q=80',
                            'alt' => 'Dr. Linda Nakamura'
                        ],
                        [
                            'name' => 'Robert O\'Brien',
                            'region' => 'Oceania Region',
                            'expertise' => 'Indigenous Rights & Cultural Preservation',
                            'image' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=400&q=80',
                            'alt' => 'Robert O\'Brien'
                        ],
                        [
                            'name' => 'Dr. Sofia Petrov',
                            'region' => 'Eastern Europe & Central Asia',
                            'expertise' => 'Economic Development & Public Policy',
                            'image' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&q=80',
                            'alt' => 'Dr. Sofia Petrov'
                        ]
                    ]),
                    'sort_order' => 5
                ]
            ],
            'call_to_action' => [
                [
                    'key' => 'title',
                    'type' => 'text',
                    'content' => 'Join the FACE Awards Community',
                    'sort_order' => 1
                ],
                [
                    'key' => 'subtitle',
                    'type' => 'text',
                    'content' => 'Be part of a global network celebrating excellence and making a positive impact across the world.',
                    'sort_order' => 2
                ],
                [
                    'key' => 'primary_button_text',
                    'type' => 'text',
                    'content' => 'Explore Current Nominees',
                    'sort_order' => 3
                ],
                [
                    'key' => 'secondary_button_text',
                    'type' => 'text',
                    'content' => 'View Award Categories',
                    'sort_order' => 4
                ]
            ]
        ],

        // APPROACH PAGE CONTENT
        'approach' => [
            'hero' => [
                [
                    'key' => 'main_title',
                    'type' => 'html',
                    'content' => 'Our Award <span class="text-face-white">Approach</span>',
                    'sort_order' => 1
                ],
                [
                    'key' => 'subtitle',
                    'type' => 'text',
                    'content' => 'A unique process designed to recognize true excellence and impact across the globe',
                    'sort_order' => 2
                ],
                [
                    'key' => 'stats_labels',
                    'type' => 'json',
                    'content' => json_encode([
                        ['key' => 'award_categories', 'label' => 'Award Categories'],
                        ['key' => 'total_nominees', 'label' => 'Total Nominees'],
                        ['key' => 'votes_cast', 'label' => 'Votes Cast']
                    ]),
                    'sort_order' => 3
                ]
            ],
            'introduction' => [
                [
                    'key' => 'title',
                    'type' => 'text',
                    'content' => 'Excellence Without Compromise',
                    'sort_order' => 1
                ],
                [
                    'key' => 'content',
                    'type' => 'html',
                    'content' => 'The FACE Awards stands apart through its commitment to fairness, inclusivity, and global representation. Our approach ensures that recognition is based on genuine impact and excellence, not influence or connections. From nomination to final celebration, each step in our process is designed to honor those who truly embody the principles of <span class="font-bold text-face-sky-blue">Focus, Achievement, Courage, and Excellence</span>.',
                    'sort_order' => 2
                ]
            ],
            'process_section' => [
                [
                    'key' => 'title',
                    'type' => 'text',
                    'content' => 'Our Recognition Process',
                    'sort_order' => 1
                ],
                [
                    'key' => 'subtitle',
                    'type' => 'text',
                    'content' => 'Five carefully crafted steps that ensure excellence is recognized fairly and globally',
                    'sort_order' => 2
                ]
            ],
            'process_steps' => [
                [
                    'key' => 'steps_data',
                    'type' => 'json',
                    'content' => json_encode([
                        [
                            'id' => 1,
                            'icon' => 'globe',
                            'title' => 'Global Reach, Local Impact',
                            'subtitle' => 'Worldwide Recognition Without Boundaries',
                            'image' => 'https://images.pexels.com/photos/1029604/pexels-photo-1029604.jpeg?auto=compress&cs=tinysrgb&w=800&h=400&fit=crop',
                            'description' => 'FACE is not limited by geography. We are committed to recognizing outstanding excellence wherever it exists — from local heroes in small communities to global brands making waves across continents.',
                            'details' => 'Our nomination process extends across borders, languages, and cultures, ensuring that all forms of excellence have the opportunity to be recognized regardless of location. Currently serving multiple regions worldwide.',
                            'color' => 'from-face-sky-blue to-face-sky-blue-light'
                        ],
                        [
                            'id' => 2,
                            'icon' => 'users',
                            'title' => 'People-Centered Nomination Process',
                            'subtitle' => 'Democratic Recognition by the People',
                            'image' => 'https://images.pexels.com/photos/1181467/pexels-photo-1181467.jpeg?auto=compress&cs=tinysrgb&w=800&h=400&fit=crop',
                            'description' => 'Nominees are selected through open, inclusive polling systems. The public votes on individuals or companies they believe are making the most impact in their respective categories.',
                            'details' => 'This democratic model ensures that recognition comes from the people who experience the impact directly. Our internal screening team then verifies that nominees meet the category criteria before advancing to the final voting round.',
                            'color' => 'from-green-500 to-teal-500'
                        ],
                        [
                            'id' => 3,
                            'icon' => 'trophy',
                            'title' => 'Award Delivery – Personal and Global',
                            'subtitle' => 'Excellence Delivered to Your Doorstep',
                            'image' => 'https://images.pexels.com/photos/8761456/pexels-photo-8761456.jpeg?auto=compress&cs=tinysrgb&w=800&h=400&fit=crop',
                            'description' => 'Once nominees are selected and voting concludes, winners receive a beautifully crafted award trophy or plaque, which is sent via secure delivery, courier, or personally presented depending on the location and circumstance.',
                            'details' => 'This approach makes sure that every honoree, regardless of their location, receives the recognition they deserve. Each award is custom-crafted to reflect the prestige and honor associated with the FACE Awards.',
                            'color' => 'from-purple-500 to-pink-500'
                        ],
                        [
                            'id' => 4,
                            'icon' => 'calendar',
                            'title' => 'End-of-Year Global Recognition Ceremony',
                            'subtitle' => 'A Grand Celebration of Excellence',
                            'image' => 'https://images.pexels.com/photos/1190298/pexels-photo-1190298.jpeg?auto=compress&cs=tinysrgb&w=800&h=400&fit=crop',
                            'description' => 'While FACE primarily delivers awards globally throughout the year, we may also host an annual end-of-year grand recognition ceremony where awardees from around the world can gather, network, and be celebrated in an atmosphere of elegance and inspiration.',
                            'details' => 'This optional event brings together diverse leaders and innovators, creating unique opportunities for collaboration and connection among those who exemplify excellence in their respective fields.',
                            'color' => 'from-orange-500 to-red-500'
                        ],
                        [
                            'id' => 5,
                            'icon' => 'handshake',
                            'title' => 'Diverse International Collaboration',
                            'subtitle' => 'United by Excellence, Strengthened by Diversity',
                            'image' => 'https://images.pexels.com/photos/3184465/pexels-photo-3184465.jpeg?auto=compress&cs=tinysrgb&w=800&h=400&fit=crop',
                            'description' => 'FACE is built on strong global partnerships. We are assembling a multicultural, multinational team of professionals, advisors, and collaborators to ensure that our work remains relevant, inclusive, and representative of diverse voices worldwide.',
                            'details' => 'This collaborative approach allows us to maintain cultural sensitivity while ensuring that our recognition standards remain consistently high across all regions and sectors we serve. We currently recognize excellence across multiple categories.',
                            'color' => 'from-indigo-500 to-blue-500'
                        ]
                    ]),
                    'sort_order' => 1
                ]
            ],
            'call_to_action' => [
                [
                    'key' => 'title',
                    'type' => 'text',
                    'content' => 'Be Part of the FACE Awards Journey',
                    'sort_order' => 1
                ],
                [
                    'key' => 'subtitle',
                    'type' => 'text',
                    'content' => 'Whether as a nominee, voter, or supporter, you can contribute to recognizing excellence around the world.',
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
                    'content' => 'Explore Award Categories',
                    'sort_order' => 4
                ]
            ]
        ],

        // AWARD PROCESS PAGE CONTENT
        'award_process' => [
            'hero' => [
                [
                    'key' => 'main_title',
                    'type' => 'text',
                    'content' => 'The Award Process',
                    'sort_order' => 1
                ],
                [
                    'key' => 'subtitle',
                    'type' => 'text',
                    'content' => 'From social media nomination to the final announcement, discover the journey of our FACE Award nominees.',
                    'sort_order' => 2
                ],
                [
                    'key' => 'background_image',
                    'type' => 'image',
                    'content' => 'https://images.unsplash.com/photo-1561489401-fc2876ced162',
                    'meta' => json_encode(['alt' => 'Award process background']),
                    'sort_order' => 3
                ]
            ],
            'process_timeline' => [
                [
                    'key' => 'steps_data',
                    'type' => 'json',
                    'content' => json_encode([
                        [
                            'id' => 1,
                            'icon' => 'share2',
                            'title' => 'Social Media Nomination',
                            'description' => 'Candidates are nominated through social media platforms using our campaign hashtags. We track mentions, shares, and engagement to identify potential nominees.',
                            'extra_content' => [
                                'type' => 'hashtags',
                                'data' => ['#FACEAwards', '#FACEImpact']
                            ],
                            'alignment' => 'right'
                        ],
                        [
                            'id' => 2,
                            'icon' => 'users',
                            'title' => 'Social Media Polls',
                            'description' => 'We conduct preliminary polls on our social media channels to gauge public interest and support for potential nominees. This helps us identify trending candidates.',
                            'extra_content' => [
                                'type' => 'poll_example',
                                'data' => [
                                    ['name' => 'Candidate A', 'percentage' => 45],
                                    ['name' => 'Candidate B', 'percentage' => 55]
                                ]
                            ],
                            'alignment' => 'left'
                        ],
                        [
                            'id' => 3,
                            'icon' => 'flag',
                            'title' => 'Internal Screening',
                            'description' => 'Our panel of experts reviews each potential nominee. We verify their credentials, assess their impact in their category, and ensure they meet our criteria for excellence.',
                            'extra_content' => [
                                'type' => 'checklist',
                                'data' => [
                                    'Verify credentials',
                                    'Assess category fit',
                                    'Evaluate impact'
                                ]
                            ],
                            'alignment' => 'right'
                        ],
                        [
                            'id' => 4,
                            'icon' => 'medal',
                            'title' => 'Nominee Shortlisting',
                            'description' => 'The top candidates who pass our internal screening are officially shortlisted. Their profiles are prepared for the public voting phase.',
                            'extra_content' => [
                                'type' => 'nominee_grid',
                                'data' => ['Nominee 1', 'Nominee 2', 'Nominee 3', 'Nominee 4', 'Nominee 5', 'Nominee 6']
                            ],
                            'alignment' => 'left'
                        ],
                        [
                            'id' => 5,
                            'icon' => 'check',
                            'title' => 'Public Voting',
                            'description' => 'Shortlisted nominees are presented on our platform for public voting. The voting period typically lasts for 30 days, during which supporters can cast their votes.',
                            'extra_content' => [
                                'type' => 'voting_period',
                                'data' => '30 Days Voting Period'
                            ],
                            'alignment' => 'right'
                        ],
                        [
                            'id' => 6,
                            'icon' => 'trophy',
                            'title' => 'Winner Announcement',
                            'description' => 'After the voting period ends, winners are announced during our prestigious award ceremony. Winners receive recognition, a digital certificate, and the iconic FACE Award trophy.',
                            'extra_content' => [
                                'type' => 'award_badge',
                                'data' => 'FACE Award Winner 2024'
                            ],
                            'alignment' => 'left'
                        ]
                    ]),
                    'sort_order' => 1
                ]
            ],
            'call_to_action' => [
                [
                    'key' => 'title',
                    'type' => 'text',
                    'content' => 'Ready to be part of our journey?',
                    'sort_order' => 1
                ],
                [
                    'key' => 'primary_button_text',
                    'type' => 'text',
                    'content' => 'View Current Nominees',
                    'sort_order' => 2
                ],
                [
                    'key' => 'secondary_button_text',
                    'type' => 'text',
                    'content' => 'Register for Next Event',
                    'sort_order' => 3
                ]
            ]
        ],

        // CATEGORIES PAGE CONTENT
        'categories' => [
            'hero' => [
                [
                    'key' => 'main_title',
                    'type' => 'html',
                    'content' => 'Award <span class="bg-gradient-to-r from-face-white via-face-sky-blue-light to-face-white bg-clip-text text-transparent">Categories</span>',
                    'sort_order' => 1
                ],
                [
                    'key' => 'subtitle',
                    'type' => 'text',
                    'content' => 'Discover the diverse categories recognizing excellence across industries and borders. Vote for your favorites!',
                    'sort_order' => 2
                ],
                [
                    'key' => 'background_image',
                    'type' => 'image',
                    'content' => 'https://images.pexels.com/photos/1181298/pexels-photo-1181298.jpeg?auto=compress&cs=tinysrgb&w=1920&h=1080&fit=crop',
                    'meta' => json_encode(['alt' => 'FACE Awards categories background']),
                    'sort_order' => 3
                ],
                [
                    'key' => 'voting_info_message',
                    'type' => 'html',
                    'content' => '<span class="font-bold">One vote per category.</span> We track IP addresses to ensure fair voting.',
                    'sort_order' => 4
                ],
                [
                    'key' => 'stats_labels',
                    'type' => 'json',
                    'content' => json_encode([
                        ['key' => 'active_categories', 'label' => 'Active Categories'],
                        ['key' => 'total_nominees', 'label' => 'Total Nominees'],
                        ['key' => 'total_votes', 'label' => 'Total Votes']
                    ]),
                    'sort_order' => 5
                ]
            ],
            'regional_navigation' => [
                [
                    'key' => 'title',
                    'type' => 'text',
                    'content' => 'Explore by Region',
                    'sort_order' => 1
                ],
                [
                    'key' => 'subtitle',
                    'type' => 'text',
                    'content' => 'Categories span across continents, celebrating global excellence',
                    'sort_order' => 2
                ],
                [
                    'key' => 'all_regions_text',
                    'type' => 'text',
                    'content' => 'All Regions',
                    'sort_order' => 3
                ]
            ],
            'categories_grid' => [
                [
                    'key' => 'title',
                    'type' => 'html',
                    'content' => 'Categories of <span class="text-face-sky-blue">Excellence</span>',
                    'sort_order' => 1
                ],
                [
                    'key' => 'subtitle',
                    'type' => 'text',
                    'content' => 'Each category represents a unique domain where exceptional achievements are recognized and celebrated',
                    'sort_order' => 2
                ],
                [
                    'key' => 'empty_state_title',
                    'type' => 'text',
                    'content' => 'No Categories Found',
                    'sort_order' => 3
                ],
                [
                    'key' => 'empty_state_message',
                    'type' => 'text',
                    'content' => 'No categories found for the selected region. Try selecting a different region.',
                    'sort_order' => 4
                ]
            ],
            'category_card_labels' => [
                [
                    'key' => 'voting_open_text',
                    'type' => 'text',
                    'content' => 'Voting Open',
                    'sort_order' => 1
                ],
                [
                    'key' => 'coming_soon_text',
                    'type' => 'text',
                    'content' => 'Coming Soon',
                    'sort_order' => 2
                ],
                [
                    'key' => 'vote_now_text',
                    'type' => 'text',
                    'content' => 'Vote Now',
                    'sort_order' => 3
                ],
                [
                    'key' => 'view_nominees_text',
                    'type' => 'text',
                    'content' => 'View Nominees',
                    'sort_order' => 4
                ],
                [
                    'key' => 'nominees_label',
                    'type' => 'text',
                    'content' => 'Nominees',
                    'sort_order' => 5
                ],
                [
                    'key' => 'votes_label',
                    'type' => 'text',
                    'content' => 'Votes',
                    'sort_order' => 6
                ],
                [
                    'key' => 'status_label',
                    'type' => 'text',
                    'content' => 'Status:',
                    'sort_order' => 7
                ],
                [
                    'key' => 'ends_in_label',
                    'type' => 'text',
                    'content' => 'Ends in:',
                    'sort_order' => 8
                ],
                [
                    'key' => 'days_suffix',
                    'type' => 'text',
                    'content' => 'day',
                    'sort_order' => 9
                ],
                [
                    'key' => 'days_suffix_plural',
                    'type' => 'text',
                    'content' => 'days',
                    'sort_order' => 10
                ]
            ],
            'voting_messages' => [
                [
                    'key' => 'already_voted_title',
                    'type' => 'text',
                    'content' => 'Already voted',
                    'sort_order' => 1
                ],
                [
                    'key' => 'already_voted_message',
                    'type' => 'text',
                    'content' => 'You have already voted in this category.',
                    'sort_order' => 2
                ],
                [
                    'key' => 'vote_success_message',
                    'type' => 'text',
                    'content' => 'Vote recorded!',
                    'sort_order' => 3
                ]
            ],
            'loading_states' => [
                [
                    'key' => 'loading_categories_text',
                    'type' => 'text',
                    'content' => 'Loading categories...',
                    'sort_order' => 1
                ],
                [
                    'key' => 'failed_to_load_text',
                    'type' => 'text',
                    'content' => 'Failed to load categories',
                    'sort_order' => 2
                ],
                [
                    'key' => 'try_again_button_text',
                    'type' => 'text',
                    'content' => 'Try Again',
                    'sort_order' => 3
                ]
            ],
            'call_to_action' => [
                [
                    'key' => 'title',
                    'type' => 'text',
                    'content' => 'Ready to Make Your Mark?',
                    'sort_order' => 1
                ],
                [
                    'key' => 'subtitle',
                    'type' => 'text',
                    'content' => 'Join the community of excellence and help recognize outstanding achievements worldwide',
                    'sort_order' => 2
                ],
                [
                    'key' => 'primary_button_text',
                    'type' => 'text',
                    'content' => 'Start Voting Now',
                    'sort_order' => 3
                ],
                [
                    'key' => 'secondary_button_text',
                    'type' => 'text',
                    'content' => 'Register for Event',
                    'sort_order' => 4
                ]
            ]
        ],

        // CONTACT PAGE CONTENT
        'contact' => [
            'hero' => [
                [
                    'key' => 'main_title',
                    'type' => 'html',
                    'content' => 'Get in <span class="text-face-sky-blue-light">Touch</span>',
                    'sort_order' => 1
                ],
                [
                    'key' => 'subtitle',
                    'type' => 'text',
                    'content' => 'Have questions about the FACE Awards? We\'re here to help you with any inquiries',
                    'sort_order' => 2
                ],
                [
                    'key' => 'background_image',
                    'type' => 'image',
                    'content' => 'https://images.pexels.com/photos/5668858/pexels-photo-5668858.jpeg?auto=compress&cs=tinysrgb&w=1920&h=1080&fit=crop',
                    'meta' => json_encode(['alt' => 'Professional contact and communication background']),
                    'sort_order' => 3
                ],
                [
                    'key' => 'stats_badges',
                    'type' => 'json',
                    'content' => json_encode([
                        ['icon' => 'clock', 'text' => '24-48 Hour Response'],
                        ['icon' => 'phone', 'text' => 'Global Support'],
                        ['icon' => 'map-pin', 'text' => 'Worldwide']
                    ]),
                    'sort_order' => 4
                ]
            ],
            'contact_form' => [
                [
                    'key' => 'form_title',
                    'type' => 'html',
                    'content' => 'Send us a <span class="text-face-sky-blue">Message</span>',
                    'sort_order' => 1
                ],
                [
                    'key' => 'form_subtitle',
                    'type' => 'text',
                    'content' => 'Fill out the form below and our team will get back to you as soon as possible.',
                    'sort_order' => 2
                ],
                [
                    'key' => 'first_name_label',
                    'type' => 'text',
                    'content' => 'First Name *',
                    'sort_order' => 3
                ],
                [
                    'key' => 'last_name_label',
                    'type' => 'text',
                    'content' => 'Last Name *',
                    'sort_order' => 4
                ],
                [
                    'key' => 'email_label',
                    'type' => 'text',
                    'content' => 'Email Address *',
                    'sort_order' => 5
                ],
                [
                    'key' => 'subject_label',
                    'type' => 'text',
                    'content' => 'Subject *',
                    'sort_order' => 6
                ],
                [
                    'key' => 'message_label',
                    'type' => 'text',
                    'content' => 'Message *',
                    'sort_order' => 7
                ],
                [
                    'key' => 'first_name_placeholder',
                    'type' => 'text',
                    'content' => 'John',
                    'sort_order' => 8
                ],
                [
                    'key' => 'last_name_placeholder',
                    'type' => 'text',
                    'content' => 'Doe',
                    'sort_order' => 9
                ],
                [
                    'key' => 'email_placeholder',
                    'type' => 'text',
                    'content' => 'john.doe@example.com',
                    'sort_order' => 10
                ],
                [
                    'key' => 'subject_placeholder',
                    'type' => 'text',
                    'content' => 'How can we help you?',
                    'sort_order' => 11
                ],
                [
                    'key' => 'message_placeholder',
                    'type' => 'text',
                    'content' => 'Please provide details about your inquiry...',
                    'sort_order' => 12
                ],
                [
                    'key' => 'submit_button_text',
                    'type' => 'text',
                    'content' => 'Send Message',
                    'sort_order' => 13
                ],
                [
                    'key' => 'sending_button_text',
                    'type' => 'text',
                    'content' => 'Sending...',
                    'sort_order' => 14
                ]
            ],
            'contact_information' => [
                [
                    'key' => 'info_title',
                    'type' => 'html',
                    'content' => 'Contact <span class="text-face-sky-blue">Information</span>',
                    'sort_order' => 1
                ],
                [
                    'key' => 'info_subtitle',
                    'type' => 'text',
                    'content' => 'Our team is available to assist you with any questions regarding nominations, event details, or general inquiries about the FACE Awards.',
                    'sort_order' => 2
                ],
                [
                    'key' => 'email_section_title',
                    'type' => 'text',
                    'content' => 'Email Us',
                    'sort_order' => 3
                ],
                [
                    'key' => 'email_general_label',
                    'type' => 'text',
                    'content' => 'For general inquiries:',
                    'sort_order' => 4
                ],
                [
                    'key' => 'email_nominations_label',
                    'type' => 'text',
                    'content' => 'For nominations:',
                    'sort_order' => 5
                ],
                [
                    'key' => 'phone_section_title',
                    'type' => 'text',
                    'content' => 'Call Us',
                    'sort_order' => 6
                ],
                [
                    'key' => 'phone_international_label',
                    'type' => 'text',
                    'content' => 'International:',
                    'sort_order' => 7
                ],
                [
                    'key' => 'phone_toll_free_label',
                    'type' => 'text',
                    'content' => 'Toll Free:',
                    'sort_order' => 8
                ],
                [
                    'key' => 'address_section_title',
                    'type' => 'text',
                    'content' => 'Visit Us',
                    'sort_order' => 9
                ],
                [
                    'key' => 'office_hours_section_title',
                    'type' => 'text',
                    'content' => 'Office Hours',
                    'sort_order' => 10
                ],
                [
                    'key' => 'response_time_label',
                    'type' => 'text',
                    'content' => 'Response Time:',
                    'sort_order' => 11
                ]
            ],
            'form_messages' => [
                [
                    'key' => 'validation_error_title',
                    'type' => 'text',
                    'content' => 'Please fill in all fields',
                    'sort_order' => 1
                ],
                [
                    'key' => 'validation_error_message',
                    'type' => 'text',
                    'content' => 'All fields are required to submit your message.',
                    'sort_order' => 2
                ],
                [
                    'key' => 'success_title',
                    'type' => 'text',
                    'content' => 'Message sent successfully!',
                    'sort_order' => 3
                ],
                [
                    'key' => 'success_message',
                    'type' => 'text',
                    'content' => 'Thank you for contacting us. We\'ll get back to you within 24-48 hours.',
                    'sort_order' => 4
                ],
                [
                    'key' => 'error_title',
                    'type' => 'text',
                    'content' => 'Failed to send message',
                    'sort_order' => 5
                ],
                [
                    'key' => 'error_message',
                    'type' => 'text',
                    'content' => 'Please try again or contact us directly at info@faceawards.org',
                    'sort_order' => 6
                ]
            ],
            'map_section' => [
                [
                    'key' => 'title',
                    'type' => 'html',
                    'content' => 'Our <span class="text-face-sky-blue">Location</span>',
                    'sort_order' => 1
                ]
            ],
            'faq_cta' => [
                [
                    'key' => 'title',
                    'type' => 'text',
                    'content' => 'Still Have Questions?',
                    'sort_order' => 1
                ],
                [
                    'key' => 'subtitle',
                    'type' => 'text',
                    'content' => 'Check out our award process or explore our categories for more information about the FACE Awards.',
                    'sort_order' => 2
                ],
                [
                    'key' => 'primary_button_text',
                    'type' => 'text',
                    'content' => 'View Award Process',
                    'sort_order' => 3
                ],
                [
                    'key' => 'secondary_button_text',
                    'type' => 'text',
                    'content' => 'Explore Categories',
                    'sort_order' => 4
                ]
            ]
        ],

        // GALLERY PAGE CONTENT
        'gallery' => [
            'hero' => [
                [
                    'key' => 'main_title',
                    'type' => 'html',
                    'content' => 'Event <span class="text-face-sky-blue-light">Gallery</span>',
                    'sort_order' => 1
                ],
                [
                    'key' => 'subtitle',
                    'type' => 'text',
                    'content' => 'Experience the grandeur and inspiration of FACE Award ceremonies from around the world',
                    'sort_order' => 2
                ],
                [
                    'key' => 'background_image',
                    'type' => 'image',
                    'content' => 'https://images.pexels.com/photos/1190298/pexels-photo-1190298.jpeg?auto=compress&cs=tinysrgb&w=1920&h=1080&fit=crop',
                    'meta' => json_encode(['alt' => 'Awards ceremony background']),
                    'sort_order' => 3
                ],
                [
                    'key' => 'stats_labels',
                    'type' => 'json',
                    'content' => json_encode([
                        ['key' => 'events', 'label' => 'Events', 'suffix' => '+'],
                        ['key' => 'photos', 'label' => 'Photos', 'suffix' => '+'],
                        ['key' => 'years', 'label' => 'Years', 'suffix' => '+']
                    ]),
                    'sort_order' => 4
                ]
            ],
            'year_selector' => [
                [
                    'key' => 'events_suffix',
                    'type' => 'text',
                    'content' => 'Events',
                    'sort_order' => 1
                ]
            ],
            'gallery_content' => [
                [
                    'key' => 'no_images_title',
                    'type' => 'text',
                    'content' => 'No Images Available',
                    'sort_order' => 1
                ],
                [
                    'key' => 'no_images_message',
                    'type' => 'text',
                    'content' => 'Images for this event are coming soon.',
                    'sort_order' => 2
                ],
                [
                    'key' => 'no_events_title',
                    'type' => 'text',
                    'content' => 'No Events Found',
                    'sort_order' => 3
                ],
                [
                    'key' => 'no_events_message_with_year',
                    'type' => 'text',
                    'content' => 'No events found for {year}. Try selecting a different year.',
                    'sort_order' => 4
                ],
                [
                    'key' => 'no_events_message_general',
                    'type' => 'text',
                    'content' => 'No events available at the moment.',
                    'sort_order' => 5
                ],
                [
                    'key' => 'image_counter_text',
                    'type' => 'text',
                    'content' => 'of',
                    'sort_order' => 6
                ]
            ],
            'loading_states' => [
                [
                    'key' => 'loading_gallery_text',
                    'type' => 'text',
                    'content' => 'Loading gallery...',
                    'sort_order' => 1
                ],
                [
                    'key' => 'failed_to_load_text',
                    'type' => 'text',
                    'content' => 'Failed to load gallery',
                    'sort_order' => 2
                ],
                [
                    'key' => 'try_again_button_text',
                    'type' => 'text',
                    'content' => 'Try Again',
                    'sort_order' => 3
                ]
            ],
            'call_to_action' => [
                [
                    'key' => 'title',
                    'type' => 'text',
                    'content' => 'Be Part of Our Next Celebration',
                    'sort_order' => 1
                ],
                [
                    'key' => 'subtitle',
                    'type' => 'text',
                    'content' => 'Join us at upcoming FACE Awards events and become part of our global community celebrating excellence.',
                    'sort_order' => 2
                ],
                [
                    'key' => 'primary_button_text',
                    'type' => 'text',
                    'content' => 'Register for Next Event',
                    'sort_order' => 3
                ],
                [
                    'key' => 'secondary_button_text',
                    'type' => 'text',
                    'content' => 'View Current Nominees',
                    'sort_order' => 4
                ]
            ]
        ],

        // IMPACT STORIES PAGE CONTENT
        'impact_stories' => [
            'hero' => [
                [
                    'key' => 'main_title',
                    'type' => 'text',
                    'content' => 'Impact Stories',
                    'sort_order' => 1
                ],
                [
                    'key' => 'subtitle',
                    'type' => 'text',
                    'content' => 'Inspirational journeys and remarkable achievements of those making a difference across the globe.',
                    'sort_order' => 2
                ],
                [
                    'key' => 'background_image',
                    'type' => 'image',
                    'content' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d',
                    'meta' => json_encode(['alt' => 'Impact stories background']),
                    'sort_order' => 3
                ]
            ],
            'stories_section' => [
                [
                    'key' => 'all_stories_text',
                    'type' => 'text',
                    'content' => 'All Stories',
                    'sort_order' => 1
                ],
                [
                    'key' => 'read_story_button_text',
                    'type' => 'text',
                    'content' => 'Read Full Story',
                    'sort_order' => 2
                ],
                [
                    'key' => 'featuring_label',
                    'type' => 'text',
                    'content' => 'Featuring:',
                    'sort_order' => 3
                ],
                [
                    'key' => 'video_badge_text',
                    'type' => 'text',
                    'content' => 'Video',
                    'sort_order' => 4
                ],
                [
                    'key' => 'interview_badge_text',
                    'type' => 'text',
                    'content' => 'Interview',
                    'sort_order' => 5
                ],
                [
                    'key' => 'article_badge_text',
                    'type' => 'text',
                    'content' => 'Article',
                    'sort_order' => 6
                ]
            ]
        ],

        // NOMINEE PROFILE PAGE CONTENT
        'nominee_profile' => [
            'navigation' => [
                [
                    'key' => 'back_to_nominees_text',
                    'type' => 'text',
                    'content' => 'Back to All Nominees',
                    'sort_order' => 1
                ],
                [
                    'key' => 'winner_badge_text',
                    'type' => 'text',
                    'content' => '🏆 Winner',
                    'sort_order' => 2
                ]
            ],
            'profile_sections' => [
                [
                    'key' => 'current_standing_title',
                    'type' => 'text',
                    'content' => 'Current Standing',
                    'sort_order' => 1
                ],
                [
                    'key' => 'vote_percentage_label',
                    'type' => 'text',
                    'content' => 'Vote Percentage',
                    'sort_order' => 2
                ],
                [
                    'key' => 'total_votes_label',
                    'type' => 'text',
                    'content' => 'Total Votes',
                    'sort_order' => 3
                ],
                [
                    'key' => 'connect_title',
                    'type' => 'text',
                    'content' => 'Connect',
                    'sort_order' => 4
                ],
                [
                    'key' => 'share_profile_title',
                    'type' => 'text',
                    'content' => 'Share Profile',
                    'sort_order' => 5
                ],
                [
                    'key' => 'impact_summary_title',
                    'type' => 'text',
                    'content' => 'Impact Summary',
                    'sort_order' => 6
                ]
            ],
            'voting_actions' => [
                [
                    'key' => 'vote_button_text',
                    'type' => 'text',
                    'content' => 'Vote for {name}',
                    'sort_order' => 1
                ],
                [
                    'key' => 'voting_text',
                    'type' => 'text',
                    'content' => 'Voting...',
                    'sort_order' => 2
                ],
                [
                    'key' => 'vote_recorded_text',
                    'type' => 'text',
                    'content' => 'Vote Recorded',
                    'sort_order' => 3
                ],
                [
                    'key' => 'vote_success_title',
                    'type' => 'text',
                    'content' => 'Vote Recorded!',
                    'sort_order' => 4
                ],
                [
                    'key' => 'vote_success_message',
                    'type' => 'text',
                    'content' => 'Thank you for voting for {name}.',
                    'sort_order' => 5
                ],
                [
                    'key' => 'vote_failed_title',
                    'type' => 'text',
                    'content' => 'Vote Failed',
                    'sort_order' => 6
                ],
                [
                    'key' => 'vote_failed_message',
                    'type' => 'text',
                    'content' => 'Failed to record vote. Please try again.',
                    'sort_order' => 7
                ]
            ],
            'tabs' => [
                [
                    'key' => 'profile_tab_text',
                    'type' => 'text',
                    'content' => 'Profile',
                    'sort_order' => 1
                ],
                [
                    'key' => 'achievements_tab_text',
                    'type' => 'text',
                    'content' => 'Achievements',
                    'sort_order' => 2
                ],
                [
                    'key' => 'testimonials_tab_text',
                    'type' => 'text',
                    'content' => 'Testimonials',
                    'sort_order' => 3
                ],
                [
                    'key' => 'media_tab_text',
                    'type' => 'text',
                    'content' => 'Media',
                    'sort_order' => 4
                ],
                [
                    'key' => 'achievement_timeline_title',
                    'type' => 'text',
                    'content' => 'Achievement Timeline',
                    'sort_order' => 5
                ],
                [
                    'key' => 'featured_video_title',
                    'type' => 'text',
                    'content' => 'Featured Video',
                    'sort_order' => 6
                ]
            ],
            'social_sharing' => [
                [
                    'key' => 'twitter_text',
                    'type' => 'text',
                    'content' => 'Twitter',
                    'sort_order' => 1
                ],
                [
                    'key' => 'facebook_text',
                    'type' => 'text',
                    'content' => 'Facebook',
                    'sort_order' => 2
                ],
                [
                    'key' => 'linkedin_text',
                    'type' => 'text',
                    'content' => 'LinkedIn',
                    'sort_order' => 3
                ]
            ],
            'error_states' => [
                [
                    'key' => 'loading_profile_text',
                    'type' => 'text',
                    'content' => 'Loading nominee profile...',
                    'sort_order' => 1
                ],
                [
                    'key' => 'nominee_not_found_title',
                    'type' => 'text',
                    'content' => 'Nominee Not Found',
                    'sort_order' => 2
                ],
                [
                    'key' => 'nominee_not_found_message',
                    'type' => 'text',
                    'content' => 'We couldn\'t find the nominee you\'re looking for.',
                    'sort_order' => 3
                ],
                [
                    'key' => 'return_to_nominees_text',
                    'type' => 'text',
                    'content' => 'Return to Nominees',
                    'sort_order' => 4
                ]
            ]
        ],

        // PAST WINNERS PAGE CONTENT
        'past_winners' => [
            'hero' => [
                [
                    'key' => 'main_title',
                    'type' => 'html',
                    'content' => 'Past Award <span class="bg-gradient-to-r from-white via-yellow-200 to-white bg-clip-text text-transparent">Winners</span>',
                    'sort_order' => 1
                ],
                [
                    'key' => 'subtitle',
                    'type' => 'text',
                    'content' => 'Celebrating the remarkable achievements of previous FACE Award recipients who exemplify Focus, Achievement, Courage, and Excellence',
                    'sort_order' => 2
                ],
                [
                    'key' => 'background_image',
                    'type' => 'image',
                    'content' => 'https://images.pexels.com/photos/1190298/pexels-photo-1190298.jpeg?auto=compress&cs=tinysrgb&w=1920&h=1080&fit=crop',
                    'meta' => json_encode(['alt' => 'FACE Awards past winners background']),
                    'sort_order' => 3
                ],
                [
                    'key' => 'stats_labels',
                    'type' => 'json',
                    'content' => json_encode([
                        ['key' => 'total_winners', 'label' => 'Total Winners'],
                        ['key' => 'organizations', 'label' => 'Organizations'],
                        ['key' => 'years_featured', 'label' => 'Years Featured']
                    ]),
                    'sort_order' => 4
                ]
            ],
            'filters_section' => [
                [
                    'key' => 'title',
                    'type' => 'text',
                    'content' => 'Explore Our Winners',
                    'sort_order' => 1
                ],
                [
                    'key' => 'subtitle',
                    'type' => 'text',
                    'content' => 'Filter by year and category to discover inspiring achievements',
                    'sort_order' => 2
                ],
                [
                    'key' => 'select_year_label',
                    'type' => 'text',
                    'content' => 'Select Year',
                    'sort_order' => 3
                ],
                [
                    'key' => 'select_category_label',
                    'type' => 'text',
                    'content' => 'Select Category',
                    'sort_order' => 4
                ],
                [
                    'key' => 'all_categories_text',
                    'type' => 'text',
                    'content' => 'All Categories',
                    'sort_order' => 5
                ]
            ],
            'winners_grid' => [
                [
                    'key' => 'winner_badge_text',
                    'type' => 'text',
                    'content' => '{year} Winner',
                    'sort_order' => 1
                ],
                [
                    'key' => 'view_details_text',
                    'type' => 'text',
                    'content' => 'View Details',
                    'sort_order' => 2
                ],
                [
                    'key' => 'organization_label',
                    'type' => 'text',
                    'content' => 'Organization:',
                    'sort_order' => 3
                ],
                [
                    'key' => 'face_award_winner_text',
                    'type' => 'text',
                    'content' => 'FACE Award Winner',
                    'sort_order' => 4
                ],
                [
                    'key' => 'results_header_text',
                    'type' => 'text',
                    'content' => '{year} Winners {category}',
                    'sort_order' => 5
                ],
                [
                    'key' => 'results_count_text',
                    'type' => 'text',
                    'content' => 'Displaying {count} award recipient{plural} who changed the world',
                    'sort_order' => 6
                ],
                [
                    'key' => 'no_winners_title',
                    'type' => 'text',
                    'content' => 'No Winners Found',
                    'sort_order' => 7
                ],
                [
                    'key' => 'no_winners_message',
                    'type' => 'text',
                    'content' => 'No winners found for the selected filters. Try selecting different criteria.',
                    'sort_order' => 8
                ]
            ],
            'loading_states' => [
                [
                    'key' => 'loading_winners_text',
                    'type' => 'text',
                    'content' => 'Loading past winners...',
                    'sort_order' => 1
                ],
                [
                    'key' => 'failed_to_load_text',
                    'type' => 'text',
                    'content' => 'Failed to load past winners',
                    'sort_order' => 2
                ],
                [
                    'key' => 'try_again_button_text',
                    'type' => 'text',
                    'content' => 'Try Again',
                    'sort_order' => 3
                ]
            ],
            'call_to_action' => [
                [
                    'key' => 'title',
                    'type' => 'text',
                    'content' => 'Inspired by Excellence?',
                    'sort_order' => 1
                ],
                [
                    'key' => 'subtitle',
                    'type' => 'text',
                    'content' => 'These winners started with a vision. Your journey to excellence could be next.',
                    'sort_order' => 2
                ],
                [
                    'key' => 'primary_button_text',
                    'type' => 'text',
                    'content' => 'Submit Nomination',
                    'sort_order' => 3
                ],
                [
                    'key' => 'secondary_button_text',
                    'type' => 'text',
                    'content' => 'View Current Nominees',
                    'sort_order' => 4
                ]
            ]
        ],

        // REGISTRATION PAGE CONTENT
        'registration' => [
            'hero' => [
                [
                    'key' => 'main_title',
                    'type' => 'html',
                    'content' => 'Event <span class="text-white">Registration</span>',
                    'sort_order' => 1
                ],
                [
                    'key' => 'subtitle',
                    'type' => 'text',
                    'content' => 'Join us for a prestigious evening celebrating excellence and achievement from around the globe',
                    'sort_order' => 2
                ],
                [
                    'key' => 'background_image',
                    'type' => 'image',
                    'content' => 'https://images.pexels.com/photos/1181298/pexels-photo-1181298.jpeg?auto=compress&cs=tinysrgb&w=1920&h=1080&fit=crop',
                    'meta' => json_encode(['alt' => 'FACE Awards registration background']),
                    'sort_order' => 3
                ],
                [
                    'key' => 'event_badges',
                    'type' => 'json',
                    'content' => json_encode([
                        ['icon' => 'calendar', 'text' => 'November 15, 2024'],
                        ['icon' => 'map-pin', 'text' => 'New York City'],
                        ['icon' => 'users', 'text' => 'Black Tie Event']
                    ]),
                    'sort_order' => 4
                ]
            ],
            'progress_steps' => [
                [
                    'key' => 'step_1_title',
                    'type' => 'text',
                    'content' => 'Personal Information',
                    'sort_order' => 1
                ],
                [
                    'key' => 'step_2_title',
                    'type' => 'text',
                    'content' => 'Ticket Selection',
                    'sort_order' => 2
                ],
                [
                    'key' => 'step_3_title',
                    'type' => 'text',
                    'content' => 'Confirmation',
                    'sort_order' => 3
                ]
            ],
            'success_messages' => [
                [
                    'key' => 'registration_complete_title',
                    'type' => 'text',
                    'content' => 'Registration Complete!',
                    'sort_order' => 1
                ],
                [
                    'key' => 'registration_complete_message',
                    'type' => 'text',
                    'content' => 'Thank you for registering for the FACE Awards ceremony. We\'ve sent a confirmation email with all the details.',
                    'sort_order' => 2
                ],
                [
                    'key' => 'your_reservation_title',
                    'type' => 'text',
                    'content' => 'Your Reservation',
                    'sort_order' => 3
                ],
                [
                    'key' => 'arrival_instruction',
                    'type' => 'text',
                    'content' => 'Please arrive 30 minutes early for registration',
                    'sort_order' => 4
                ],
                [
                    'key' => 'ticket_type_label',
                    'type' => 'text',
                    'content' => 'Ticket Type:',
                    'sort_order' => 5
                ],
                [
                    'key' => 'price_label',
                    'type' => 'text',
                    'content' => 'Price:',
                    'sort_order' => 6
                ],
                [
                    'key' => 'reference_label',
                    'type' => 'text',
                    'content' => 'Reference #:',
                    'sort_order' => 7
                ],
                [
                    'key' => 'return_home_button_text',
                    'type' => 'text',
                    'content' => 'Return to Home',
                    'sort_order' => 8
                ]
            ],
            'event_details' => [
                [
                    'key' => 'section_title',
                    'type' => 'text',
                    'content' => 'Event Details',
                    'sort_order' => 1
                ],
                [
                    'key' => 'date_time_title',
                    'type' => 'text',
                    'content' => 'Date & Time',
                    'sort_order' => 2
                ],
                [
                    'key' => 'location_title',
                    'type' => 'text',
                    'content' => 'Location',
                    'sort_order' => 3
                ],
                [
                    'key' => 'dress_code_title',
                    'type' => 'text',
                    'content' => 'Dress Code',
                    'sort_order' => 4
                ],
                [
                    'key' => 'dress_code_text',
                    'type' => 'text',
                    'content' => 'Black Tie / Formal Evening Wear',
                    'sort_order' => 5
                ],
                [
                    'key' => 'contact_title',
                    'type' => 'text',
                    'content' => 'Contact',
                    'sort_order' => 6
                ],
                [
                    'key' => 'contact_message',
                    'type' => 'html',
                    'content' => 'For any questions or assistance:<br><a href="mailto:events@faceawards.org" class="text-face-sky-blue hover:underline font-medium">events@faceawards.org</a>',
                    'sort_order' => 7
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
