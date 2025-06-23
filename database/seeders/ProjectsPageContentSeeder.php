<?php
// database/seeders/ProjectsPageContentSeeder.php

namespace Database\Seeders;

use App\Models\PageContent;
use Illuminate\Database\Seeder;

class ProjectsPageContentSeeder extends Seeder
{
    public function run()
    {
        $projectsContent = [
            'projects' => [
                'hero' => [
                    [
                        'key' => 'main_title',
                        'type' => 'html',
                        'content' => 'Our <span class="text-face-sky-blue-light">Projects</span>',
                        'sort_order' => 1
                    ],
                    [
                        'key' => 'subtitle',
                        'type' => 'text',
                        'content' => 'As a non-profit organization, all donations and support are directed toward the implementation of noble, community-based projects planned for the next two years (2025–2026).',
                        'sort_order' => 2
                    ],
                    [
                        'key' => 'background_image',
                        'type' => 'image',
                        'content' => 'https://images.unsplash.com/photo-1559027615-cd4628902d4a?auto=compress&cs=tinysrgb&w=1920&h=1080&fit=crop',
                        'meta' => json_encode(['alt' => 'Community projects background', 'caption' => 'Building stronger communities together']),
                        'sort_order' => 3
                    ],
                    [
                        'key' => 'focus_areas',
                        'type' => 'json',
                        'content' => json_encode([
                            ['icon' => 'users', 'label' => 'Community Impact'],
                            ['icon' => 'heart', 'label' => 'Social Justice'],
                            ['icon' => 'sprout', 'label' => 'Sustainability']
                        ]),
                        'sort_order' => 4
                    ]
                ],
                'introduction' => [
                    [
                        'key' => 'title',
                        'type' => 'text',
                        'content' => 'Our Focus Areas',
                        'sort_order' => 1
                    ],
                    [
                        'key' => 'content',
                        'type' => 'text',
                        'content' => 'Our focus areas include support for the homeless, women\'s empowerment, sustainable farming, and social justice. Each project is designed to create lasting impact and positive change in communities.',
                        'sort_order' => 2
                    ],
                    [
                        'key' => 'focus_cards',
                        'type' => 'json',
                        'content' => json_encode([
                            [
                                'icon' => 'users',
                                'title' => 'For the Homeless',
                                'description' => 'Mobile services and transitional housing'
                            ],
                            [
                                'icon' => 'heart',
                                'title' => 'For Women',
                                'description' => 'Empowerment and support programs'
                            ],
                            [
                                'icon' => 'sprout',
                                'title' => 'Farming & Food',
                                'description' => 'Sustainable agriculture initiatives'
                            ],
                            [
                                'icon' => 'scale',
                                'title' => 'Social Justice',
                                'description' => 'Legal aid and community support'
                            ]
                        ]),
                        'sort_order' => 3
                    ]
                ],
                'for_homeless' => [
                    [
                        'key' => 'title',
                        'type' => 'text',
                        'content' => 'For the Homeless',
                        'sort_order' => 1
                    ],
                    [
                        'key' => 'subtitle',
                        'type' => 'text',
                        'content' => 'Comprehensive support programs addressing immediate needs and long-term housing solutions',
                        'sort_order' => 2
                    ],
                    [
                        'key' => 'projects',
                        'type' => 'json',
                        'content' => json_encode([
                            [
                                'id' => 1,
                                'icon' => 'truck',
                                'title' => 'Mobile Shower and Hygiene Unit',
                                'description' => 'A mobile truck or trailer equipped with showers, toilets, and hygiene supplies.',
                                'estimated_cost' => '$75,000–$120,000 (initial setup); $5,000/month (operational)',
                                'impact' => 'Serves 100–200 people per week',
                                'timeline' => '2025',
                                'status' => 'Planning'
                            ],
                            [
                                'id' => 2,
                                'icon' => 'home',
                                'title' => 'Transitional Tiny Home Village',
                                'description' => 'Build 10–20 tiny homes with shared facilities for people transitioning from homelessness.',
                                'estimated_cost' => '$500,000 (20 units + infrastructure)',
                                'impact' => '20–30 individuals housed annually',
                                'timeline' => '2025-2026',
                                'status' => 'Planning'
                            ],
                            [
                                'id' => 3,
                                'icon' => 'briefcase',
                                'title' => 'Homeless Employment & Skill Center',
                                'description' => 'Job readiness training, resume help, digital literacy, and paid internships.',
                                'estimated_cost' => '$150,000/year',
                                'impact' => '75–150 people trained yearly',
                                'timeline' => '2025',
                                'status' => 'Planning'
                            ]
                        ]),
                        'sort_order' => 3
                    ]
                ],
                'for_women' => [
                    [
                        'key' => 'title',
                        'type' => 'text',
                        'content' => 'For Women',
                        'sort_order' => 1
                    ],
                    [
                        'key' => 'subtitle',
                        'type' => 'text',
                        'content' => 'Empowerment programs providing safety, education, and economic opportunities for women',
                        'sort_order' => 2
                    ],
                    [
                        'key' => 'projects',
                        'type' => 'json',
                        'content' => json_encode([
                            [
                                'id' => 1,
                                'icon' => 'shield',
                                'title' => 'Women\'s Safe Haven & Resource Center',
                                'description' => 'Emergency shelter and long-term counseling for survivors of domestic violence or trafficking.',
                                'estimated_cost' => '$400,000/year (leasing + staff + services)',
                                'impact' => '200–300 women served annually',
                                'timeline' => '2025',
                                'status' => 'Planning'
                            ],
                            [
                                'id' => 2,
                                'icon' => 'sprout',
                                'title' => 'Women in Farming Program',
                                'description' => 'Training and land access for women to learn sustainable agriculture and start micro-farms.',
                                'estimated_cost' => '$180,000/year (includes tools, land lease, and stipends)',
                                'impact' => '30–50 women per cohort',
                                'timeline' => '2025-2026',
                                'status' => 'Planning'
                            ],
                            [
                                'id' => 3,
                                'icon' => 'users',
                                'title' => 'Single Mother Support Network',
                                'description' => 'Monthly stipends, childcare access, financial literacy, and mentorship.',
                                'estimated_cost' => '$120,000/year (support for 40–60 mothers)',
                                'impact' => 'Empowered and stable single-mother households',
                                'timeline' => '2025',
                                'status' => 'Planning'
                            ]
                        ]),
                        'sort_order' => 3
                    ]
                ],
                'farming_food_justice' => [
                    [
                        'key' => 'title',
                        'type' => 'text',
                        'content' => 'Farming & Food Justice',
                        'sort_order' => 1
                    ],
                    [
                        'key' => 'subtitle',
                        'type' => 'text',
                        'content' => 'Sustainable agriculture and food security initiatives for communities in need',
                        'sort_order' => 2
                    ],
                    [
                        'key' => 'projects',
                        'type' => 'json',
                        'content' => json_encode([
                            [
                                'id' => 1,
                                'icon' => 'sprout',
                                'title' => 'Community Garden & Urban Farming Initiative',
                                'description' => 'Convert vacant lots into urban farms with local community members growing and selling food.',
                                'estimated_cost' => '$50,000 per site',
                                'impact' => 'Improves food access for 100+ families per site',
                                'timeline' => '2025',
                                'status' => 'Planning'
                            ],
                            [
                                'id' => 2,
                                'icon' => 'graduationcap',
                                'title' => 'Youth Farming Apprenticeship Program',
                                'description' => 'Engage at-risk youth in sustainable farming with paid apprenticeships.',
                                'estimated_cost' => '$80,000/year (tools, stipends, training)',
                                'impact' => '25–40 youth per year',
                                'timeline' => '2025-2026',
                                'status' => 'Planning'
                            ],
                            [
                                'id' => 3,
                                'icon' => 'utensils',
                                'title' => 'Food Rescue & Redistribution Hub',
                                'description' => 'Collect surplus food from farms/restaurants and distribute to shelters and low-income families.',
                                'estimated_cost' => '$100,000/year (van, staff, storage)',
                                'impact' => '50,000+ meals per year',
                                'timeline' => '2025',
                                'status' => 'Planning'
                            ]
                        ]),
                        'sort_order' => 3
                    ]
                ],
                'social_justice' => [
                    [
                        'key' => 'title',
                        'type' => 'text',
                        'content' => 'Social Justice',
                        'sort_order' => 1
                    ],
                    [
                        'key' => 'subtitle',
                        'type' => 'text',
                        'content' => 'Legal aid, restorative justice, and civic engagement programs for marginalized communities',
                        'sort_order' => 2
                    ],
                    [
                        'key' => 'projects',
                        'type' => 'json',
                        'content' => json_encode([
                            [
                                'id' => 1,
                                'icon' => 'scale',
                                'title' => 'Community Legal Aid Clinic',
                                'description' => 'Free legal support for housing, immigration, and civil rights cases.',
                                'estimated_cost' => '$250,000/year (lawyers, paralegals, outreach)',
                                'impact' => '300–500 clients served annually',
                                'timeline' => '2025',
                                'status' => 'Planning'
                            ],
                            [
                                'id' => 2,
                                'icon' => 'usercheck',
                                'title' => 'Restorative Justice & Mediation Circles',
                                'description' => 'Train facilitators to host community peacebuilding and justice alternatives.',
                                'estimated_cost' => '$50,000/year',
                                'impact' => 'Serves schools, families, and justice-involved youth',
                                'timeline' => '2025-2026',
                                'status' => 'Planning'
                            ],
                            [
                                'id' => 3,
                                'icon' => 'bookopen',
                                'title' => 'Civic Engagement Bootcamps for Marginalized Groups',
                                'description' => 'Workshops on voting rights, organizing, leadership, and policy influence.',
                                'estimated_cost' => '$60,000/year',
                                'impact' => 'Trains 100–200 residents per cycle',
                                'timeline' => '2025',
                                'status' => 'Planning'
                            ]
                        ]),
                        'sort_order' => 3
                    ]
                ],
                'call_to_action' => [
                    [
                        'key' => 'title',
                        'type' => 'text',
                        'content' => 'Support Our Mission',
                        'sort_order' => 1
                    ],
                    [
                        'key' => 'subtitle',
                        'type' => 'text',
                        'content' => 'Your support helps us implement these vital community projects. Together, we can create lasting positive change in the lives of those who need it most.',
                        'sort_order' => 2
                    ],
                    [
                        'key' => 'primary_button_text',
                        'type' => 'text',
                        'content' => 'Get Involved',
                        'sort_order' => 3
                    ],
                    [
                        'key' => 'secondary_button_text',
                        'type' => 'text',
                        'content' => 'Learn More About Us',
                        'sort_order' => 4
                    ]
                ]
            ]
        ];

        // Insert projects content
        foreach ($projectsContent as $page => $sections) {
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

        $this->command->info('Projects page content seeded successfully!');
    }
}
