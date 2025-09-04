<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Application;
use App\Models\User;
use App\Models\Scholarship;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Application>
 */
class ApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'application_data' => $this->generateBasicApplicationData(),
            'status' => $this->faker->randomElement(['draft', 'submitted', 'under_review', 'approved', 'rejected']),
            'committee_notes' => null,
            'submitted_at' => null,
            'reviewed_at' => null,
            'reviewed_by' => null,
            'created_at' => $this->faker->dateTimeBetween('-60 days', '-1 days'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    /**
     * Configure the factory for a specific scholarship and user
     */
    public function forScholarshipAndUser(Scholarship $scholarship, User $user): static
    {
        return $this->state(function (array $attributes) use ($scholarship, $user) {
            return [
                'scholarship_id' => $scholarship->id,
                'user_id' => $user->id,
                'application_data' => $this->generateScholarshipSpecificData($scholarship, $user),
            ];
        });
    }

    /**
     * Generate Merit scholarship specific data
     */
    public function merit(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'application_data' => json_encode([
                    'personal_information' => [
                        'full_name' => $this->faker->name(),
                        'date_of_birth' => $this->faker->date('Y-m-d', '2005-01-01'),
                        'place_of_birth' => 'Daanbantayan, Cebu',
                        'gender' => $this->faker->randomElement(['Male', 'Female']),
                        'civil_status' => 'Single',
                        'nationality' => 'Filipino',
                        'contact_number' => '+63 9' . $this->faker->numberBetween(100000000, 999999999),
                        'email_address' => $this->faker->email(),
                        'permanent_address' => $this->faker->address()
                    ],
                    'academic_information' => [
                        'current_gpa' => $this->faker->numberBetween(350, 400) / 100, // 3.50 to 4.00 for merit
                        'class_rank' => $this->faker->numberBetween(1, 10) . '/' . $this->faker->numberBetween(50, 200),
                        'school' => $this->faker->randomElement([
                            'University of Cebu - Main Campus',
                            'Cebu Institute of Technology',
                            'University of San Carlos',
                            'Southwestern University'
                        ]),
                        'course' => $this->faker->randomElement([
                            'BS Computer Science',
                            'BS Information Technology',
                            'BS Engineering',
                            'BS Mathematics',
                            'BS Physics'
                        ]),
                        'year_level' => $this->faker->randomElement(['1st Year', '2nd Year', '3rd Year', '4th Year']),
                        'expected_graduation' => $this->faker->date('Y-m-d', '+3 years')
                    ],
                    'achievements' => [
                        'academic_awards' => $this->faker->randomElements([
                            'Dean\'s List - Semester 1',
                            'Academic Excellence Award',
                            'Outstanding Student in Mathematics',
                            'Best Thesis Award',
                            'Magna Cum Laude'
                        ], $this->faker->numberBetween(1, 3)),
                        'extracurricular_activities' => $this->faker->randomElements([
                            'Student Government Secretary',
                            'Computer Science Society Member',
                            'Debate Club President',
                            'Math Olympiad Participant'
                        ], $this->faker->numberBetween(1, 2)),
                        'leadership_roles' => $this->faker->randomElements([
                            'Class President',
                            'Organization Vice President',
                            'Event Coordinator'
                        ], $this->faker->numberBetween(0, 2))
                    ],
                    'essay_responses' => [
                        'career_goals' => 'I aspire to become a software engineer and contribute to the technological advancement of our community.',
                        'community_involvement' => 'I have volunteered in local literacy programs and organized coding workshops for high school students.',
                        'why_deserving' => 'My consistent academic performance and dedication to community service demonstrate my commitment to excellence.'
                    ]
                ])
            ];
        });
    }

    /**
     * Generate Sports scholarship specific data
     */
    public function sports(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'application_data' => json_encode([
                    'personal_information' => [
                        'full_name' => $this->faker->name(),
                        'date_of_birth' => $this->faker->date('Y-m-d', '2005-01-01'),
                        'place_of_birth' => 'Daanbantayan, Cebu',
                        'gender' => $this->faker->randomElement(['Male', 'Female']),
                        'civil_status' => 'Single',
                        'nationality' => 'Filipino',
                        'contact_number' => '+63 9' . $this->faker->numberBetween(100000000, 999999999),
                        'height' => $this->faker->numberBetween(150, 190) . ' cm',
                        'weight' => $this->faker->numberBetween(45, 85) . ' kg',
                        'emergency_contact' => $this->faker->name(),
                        'emergency_phone' => '+63 9' . $this->faker->numberBetween(100000000, 999999999)
                    ],
                    'academic_information' => [
                        'current_gpa' => $this->faker->numberBetween(250, 350) / 100, // 2.50 to 3.50 for sports
                        'school' => $this->faker->randomElement([
                            'University of Cebu - Main Campus',
                            'Cebu Institute of Technology',
                            'University of San Carlos'
                        ]),
                        'course' => $this->faker->randomElement([
                            'BS Physical Education',
                            'BS Sports Science',
                            'BS Kinesiology',
                            'BS Recreation',
                            'BS Business Administration'
                        ]),
                        'year_level' => $this->faker->randomElement(['1st Year', '2nd Year', '3rd Year', '4th Year'])
                    ],
                    'sports_information' => [
                        'primary_sport' => $this->faker->randomElement([
                            'Basketball',
                            'Volleyball',
                            'Football',
                            'Swimming',
                            'Track and Field',
                            'Badminton',
                            'Table Tennis'
                        ]),
                        'secondary_sports' => $this->faker->randomElements([
                            'Basketball',
                            'Volleyball',
                            'Swimming',
                            'Tennis',
                            'Badminton'
                        ], $this->faker->numberBetween(0, 2)),
                        'years_experience' => $this->faker->numberBetween(3, 12) . ' years',
                        'position_played' => $this->faker->randomElement([
                            'Point Guard',
                            'Forward',
                            'Center',
                            'Midfielder',
                            'Goalkeeper',
                            'Libero',
                            'Spiker'
                        ]),
                        'team_affiliation' => 'School Varsity Team',
                        'coach_name' => $this->faker->name(),
                        'coach_contact' => '+63 9' . $this->faker->numberBetween(100000000, 999999999)
                    ],
                    'achievements' => [
                        'competitions' => $this->faker->randomElements([
                            'Regional Championships - Gold Medal',
                            'Inter-School Sports Fest - MVP',
                            'Provincial Meet - Silver Medal',
                            'National Youth Games - Participant',
                            'CESAFI Tournament - Champion'
                        ], $this->faker->numberBetween(1, 3)),
                        'awards' => $this->faker->randomElements([
                            'Most Valuable Player',
                            'Best Rookie Award',
                            'Sportsmanship Award',
                            'Team Captain'
                        ], $this->faker->numberBetween(0, 2)),
                        'records_held' => $this->faker->randomElements([
                            'School Record in 100m Sprint',
                            'Highest Scoring Average',
                            'Most Assists in a Season'
                        ], $this->faker->numberBetween(0, 1))
                    ],
                    'training_schedule' => [
                        'training_hours_per_week' => $this->faker->numberBetween(15, 25),
                        'training_days' => ['Monday', 'Tuesday', 'Thursday', 'Friday'],
                        'competition_schedule' => 'Weekends and holidays',
                        'fitness_level' => $this->faker->randomElement(['Excellent', 'Very Good', 'Good'])
                    ]
                ])
            ];
        });
    }

    /**
     * Generate Need-based scholarship specific data
     */
    public function needBased(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'application_data' => json_encode([
                    'personal_information' => [
                        'full_name' => $this->faker->name(),
                        'date_of_birth' => $this->faker->date('Y-m-d', '2005-01-01'),
                        'place_of_birth' => 'Daanbantayan, Cebu',
                        'gender' => $this->faker->randomElement(['Male', 'Female']),
                        'civil_status' => 'Single',
                        'nationality' => 'Filipino',
                        'contact_number' => '+63 9' . $this->faker->numberBetween(100000000, 999999999),
                        'permanent_address' => $this->faker->address(),
                        'current_address' => $this->faker->address()
                    ],
                    'academic_information' => [
                        'current_gpa' => $this->faker->numberBetween(250, 350) / 100, // 2.50 to 3.50
                        'school' => $this->faker->randomElement([
                            'Daanbantayan National High School',
                            'Local Public University',
                            'State University Branch'
                        ]),
                        'course' => $this->faker->randomElement([
                            'BS Education',
                            'BS Social Work',
                            'BS Agriculture',
                            'BS Nursing',
                            'BS Business Administration'
                        ]),
                        'year_level' => $this->faker->randomElement(['1st Year', '2nd Year', '3rd Year', '4th Year'])
                    ],
                    'family_information' => [
                        'father_name' => $this->faker->name('male'),
                        'father_occupation' => $this->faker->randomElement([
                            'Farmer',
                            'Fisherman',
                            'Tricycle Driver',
                            'Construction Worker',
                            'Unemployed',
                            'Deceased'
                        ]),
                        'father_monthly_income' => $this->faker->randomElement([
                            '₱5,000',
                            '₱8,000',
                            '₱12,000',
                            '₱15,000',
                            'None'
                        ]),
                        'mother_name' => $this->faker->name('female'),
                        'mother_occupation' => $this->faker->randomElement([
                            'Housewife',
                            'Market Vendor',
                            'Domestic Helper',
                            'Seamstress',
                            'Unemployed',
                            'OFW'
                        ]),
                        'mother_monthly_income' => $this->faker->randomElement([
                            '₱3,000',
                            '₱6,000',
                            '₱10,000',
                            '₱20,000',
                            'None'
                        ]),
                        'number_of_siblings' => $this->faker->numberBetween(2, 6),
                        'siblings_in_school' => $this->faker->numberBetween(1, 3),
                        'family_monthly_income' => $this->faker->randomElement([
                            '₱8,000',
                            '₱12,000',
                            '₱18,000',
                            '₱25,000',
                            '₱30,000'
                        ])
                    ],
                    'financial_information' => [
                        'current_financial_aid' => $this->faker->randomElement([
                            'None',
                            'Government Scholarship',
                            'School Grant',
                            'Private Scholarship'
                        ]),
                        'monthly_expenses' => [
                            'tuition' => '₱8,000',
                            'books_supplies' => '₱1,500',
                            'transportation' => '₱2,000',
                            'food' => '₱4,000',
                            'miscellaneous' => '₱1,000'
                        ],
                        'source_of_funding' => $this->faker->randomElement([
                            'Family Income',
                            'Part-time Job',
                            'Loans',
                            'Extended Family Help'
                        ]),
                        'financial_difficulties' => $this->faker->randomElements([
                            'Insufficient family income',
                            'Multiple siblings in school',
                            'Parent unemployment',
                            'Medical expenses',
                            'Natural disaster impact'
                        ], $this->faker->numberBetween(1, 3))
                    ],
                    'essay_responses' => [
                        'financial_need_explanation' => 'Our family struggles financially due to my father\'s unstable income as a farmer and the need to support multiple children in school.',
                        'how_scholarship_helps' => 'This scholarship would allow me to focus on my studies without worrying about financial constraints and help me achieve my dream of becoming a teacher.',
                        'commitment_to_studies' => 'I am committed to maintaining good grades and actively participating in community service to give back to our community.'
                    ]
                ])
            ];
        });
    }

    /**
     * Generate Indigenous scholarship specific data
     */
    public function indigenous(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'application_data' => json_encode([
                    'personal_information' => [
                        'full_name' => $this->faker->name(),
                        'date_of_birth' => $this->faker->date('Y-m-d', '2005-01-01'),
                        'place_of_birth' => 'Daanbantayan, Cebu',
                        'gender' => $this->faker->randomElement(['Male', 'Female']),
                        'civil_status' => 'Single',
                        'nationality' => 'Filipino',
                        'indigenous_group' => $this->faker->randomElement([
                            'Bisaya',
                            'Cebuano Indigenous',
                            'Local Tribal Community'
                        ]),
                        'tribal_affiliation' => $this->faker->randomElement([
                            'Recognized Tribal Member',
                            'Indigenous Cultural Group Member'
                        ]),
                        'contact_number' => '+63 9' . $this->faker->numberBetween(100000000, 999999999)
                    ],
                    'academic_information' => [
                        'current_gpa' => $this->faker->numberBetween(250, 350) / 100,
                        'school' => $this->faker->randomElement([
                            'Daanbantayan National High School',
                            'Local Indigenous School',
                            'Community College'
                        ]),
                        'course' => $this->faker->randomElement([
                            'BS Anthropology',
                            'BS Education',
                            'BS Agriculture',
                            'BS Social Work',
                            'BS Environmental Science'
                        ]),
                        'year_level' => $this->faker->randomElement(['1st Year', '2nd Year', '3rd Year', '4th Year']),
                        'previous_education' => 'Indigenous Community School System'
                    ],
                    'cultural_information' => [
                        'cultural_practices' => $this->faker->randomElements([
                            'Traditional farming methods',
                            'Indigenous language preservation',
                            'Cultural dance and music',
                            'Traditional crafts and weaving',
                            'Ancestral domain stewardship'
                        ], $this->faker->numberBetween(2, 4)),
                        'language_spoken' => $this->faker->randomElement([
                            'Cebuano and indigenous dialect',
                            'Local tribal language',
                            'Bisayan indigenous variant'
                        ]),
                        'community_role' => $this->faker->randomElement([
                            'Youth Cultural Leader',
                            'Traditional Dance Performer',
                            'Language Preservation Advocate',
                            'Community Volunteer'
                        ]),
                        'cultural_preservation_activities' => $this->faker->randomElements([
                            'Teaching traditional dances to younger generation',
                            'Participating in cultural festivals',
                            'Documenting indigenous stories and legends',
                            'Assisting in community cultural events'
                        ], $this->faker->numberBetween(1, 3))
                    ],
                    'family_background' => [
                        'parents_indigenous_status' => 'Both parents are members of indigenous community',
                        'family_occupation' => $this->faker->randomElement([
                            'Subsistence farming',
                            'Traditional fishing',
                            'Handicraft making',
                            'Forest stewardship'
                        ]),
                        'community_location' => 'Indigenous ancestral domain in Daanbantayan',
                        'family_cultural_role' => $this->faker->randomElement([
                            'Cultural knowledge keepers',
                            'Traditional healers',
                            'Community leaders',
                            'Ancestral domain caretakers'
                        ])
                    ],
                    'goals_and_aspirations' => [
                        'career_goals' => 'To become an advocate for indigenous rights and cultural preservation while contributing to my community\'s development.',
                        'community_commitment' => 'I plan to return to my community and use my education to help preserve our culture and improve living conditions.',
                        'cultural_preservation_plans' => 'Establish programs for indigenous youth education and cultural awareness.'
                    ]
                ])
            ];
        });
    }

    /**
     * Application with submitted status
     */
    public function submitted(): static
    {
        return $this->state(function (array $attributes) {
            $submittedAt = $this->faker->dateTimeBetween('-30 days', '-7 days');

            return [
                'status' => 'submitted',
                'submitted_at' => $submittedAt,
                'updated_at' => $submittedAt,
            ];
        });
    }

    /**
     * Application under review
     */
    public function underReview(): static
    {
        return $this->state(function (array $attributes) {
            $submittedAt = $this->faker->dateTimeBetween('-20 days', '-10 days');

            return [
                'status' => 'under_review',
                'submitted_at' => $submittedAt,
                'committee_notes' => $this->faker->randomElement([
                    'Application is complete and under committee review.',
                    'Reviewing academic credentials and supporting documents.',
                    'Awaiting additional verification from school.',
                    'Committee meeting scheduled for final decision.'
                ]),
                'updated_at' => $this->faker->dateTimeBetween($submittedAt, 'now'),
            ];
        });
    }

    /**
     * Approved application
     */
    public function approved(): static
    {
        return $this->state(function (array $attributes) {
            $submittedAt = $this->faker->dateTimeBetween('-45 days', '-20 days');
            $reviewedAt = $this->faker->dateTimeBetween($submittedAt, '-5 days');

            return [
                'status' => 'approved',
                'submitted_at' => $submittedAt,
                'reviewed_at' => $reviewedAt,
                'committee_notes' => $this->faker->randomElement([
                    'Application approved. Excellent academic performance and strong community involvement.',
                    'Committee unanimously approved this outstanding application.',
                    'Meets all requirements with exceptional qualifications.',
                    'Approved for full scholarship benefits. Congratulations!'
                ]),
                'updated_at' => $reviewedAt,
            ];
        });
    }

    /**
     * Rejected application
     */
    public function rejected(): static
    {
        return $this->state(function (array $attributes) {
            $submittedAt = $this->faker->dateTimeBetween('-30 days', '-15 days');
            $reviewedAt = $this->faker->dateTimeBetween($submittedAt, '-3 days');

            return [
                'status' => 'rejected',
                'submitted_at' => $submittedAt,
                'reviewed_at' => $reviewedAt,
                'committee_notes' => $this->faker->randomElement([
                    'Application does not meet minimum GPA requirements.',
                    'Incomplete documentation submitted.',
                    'Financial need criteria not sufficiently demonstrated.',
                    'Application submitted after deadline.'
                ]),
                'updated_at' => $reviewedAt,
            ];
        });
    }

    /**
     * Draft application (in progress)
     */
    public function draft(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'draft',
                'submitted_at' => null,
                'reviewed_at' => null,
                'committee_notes' => null,
            ];
        });
    }

    /**
     * Application with reviewer assigned
     */
    public function withReviewer(User $reviewer): static
    {
        return $this->state(function (array $attributes) use ($reviewer) {
            return [
                'reviewed_by' => $reviewer->id,
            ];
        });
    }

    /**
     * Generate scholarship-specific application data
     */
    private function generateScholarshipSpecificData(Scholarship $scholarship, User $user): string
    {
        $data = match ($scholarship->slug) {
            'merit-scholarship' => $this->generateMeritData($user),
            'sports-scholarship' => $this->generateSportsData($user),
            'need-based-scholarship' => $this->generateNeedBasedData($user),
            'indigenous-scholarship' => $this->generateIndigenousData($user),
            default => $this->generateBasicApplicationData()
        };

        return json_encode($data);
    }

    /**
     * Generate basic application data (fallback)
     */
    private function generateBasicApplicationData(): string
    {
        return json_encode([
            'personal_information' => [
                'full_name' => $this->faker->name(),
                'date_of_birth' => $this->faker->date('Y-m-d', '2005-01-01'),
                'gender' => $this->faker->randomElement(['Male', 'Female']),
                'civil_status' => 'Single',
                'contact_number' => '+63 9' . $this->faker->numberBetween(100000000, 999999999),
            ],
            'academic_information' => [
                'current_gpa' => $this->faker->numberBetween(250, 400) / 100,
                'school' => 'General University',
                'course' => 'General Program',
                'year_level' => $this->faker->randomElement(['1st Year', '2nd Year', '3rd Year', '4th Year'])
            ]
        ]);
    }

    private function generateMeritData(User $user): array
    {
        return [
            'personal_information' => [
                'full_name' => $user->name,
                'date_of_birth' => $this->faker->date('Y-m-d', '2005-01-01'),
                'place_of_birth' => 'Daanbantayan, Cebu',
                'gender' => $this->faker->randomElement(['Male', 'Female']),
                'civil_status' => 'Single',
                'nationality' => 'Filipino'
            ],
            'academic_information' => [
                'current_gpa' => $this->faker->numberBetween(350, 400) / 100,
                'class_rank' => $this->faker->numberBetween(1, 10) . '/' . $this->faker->numberBetween(50, 200),
                'school' => 'University of Cebu',
                'course' => 'BS Computer Science',
                'year_level' => $this->faker->randomElement(['1st Year', '2nd Year', '3rd Year', '4th Year'])
            ],
            'achievements' => [
                'academic_awards' => ['Dean\'s List', 'Academic Excellence Award'],
                'extracurricular_activities' => ['Student Government', 'Computer Society'],
                'leadership_roles' => ['Class President']
            ]
        ];
    }

    private function generateSportsData(User $user): array
    {
        return [
            'personal_information' => [
                'full_name' => $user->name,
                'date_of_birth' => $this->faker->date('Y-m-d', '2005-01-01'),
                'gender' => $this->faker->randomElement(['Male', 'Female']),
                'height' => $this->faker->numberBetween(150, 190) . ' cm',
                'weight' => $this->faker->numberBetween(45, 85) . ' kg'
            ],
            'sports_information' => [
                'primary_sport' => $this->faker->randomElement(['Basketball', 'Volleyball', 'Swimming']),
                'years_experience' => $this->faker->numberBetween(3, 12) . ' years',
                'team_affiliation' => 'School Varsity Team'
            ],
            'achievements' => [
                'competitions' => ['Regional Championships - Gold Medal'],
                'awards' => ['Most Valuable Player']
            ]
        ];
    }

    private function generateNeedBasedData(User $user): array
    {
        return [
            'personal_information' => [
                'full_name' => $user->name,
                'date_of_birth' => $this->faker->date('Y-m-d', '2005-01-01'),
                'gender' => $this->faker->randomElement(['Male', 'Female']),
            ],
            'family_information' => [
                'father_occupation' => $this->faker->randomElement(['Farmer', 'Fisherman', 'Driver']),
                'mother_occupation' => $this->faker->randomElement(['Housewife', 'Vendor', 'Helper']),
                'family_monthly_income' => '₱15,000',
                'number_of_siblings' => $this->faker->numberBetween(2, 6)
            ],
            'financial_information' => [
                'monthly_expenses' => [
                    'tuition' => '₱8,000',
                    'transportation' => '₱2,000',
                    'food' => '₱4,000'
                ]
            ]
        ];
    }

    private function generateIndigenousData(User $user): array
    {
        return [
            'personal_information' => [
                'full_name' => $user->name,
                'date_of_birth' => $this->faker->date('Y-m-d', '2005-01-01'),
                'indigenous_group' => 'Cebuano Indigenous',
                'tribal_affiliation' => 'Recognized Tribal Member'
            ],
            'cultural_information' => [
                'cultural_practices' => ['Traditional farming', 'Cultural preservation'],
                'language_spoken' => 'Cebuano and indigenous dialect',
                'community_role' => 'Youth Cultural Leader'
            ],
            'goals_and_aspirations' => [
                'career_goals' => 'Indigenous rights advocate and cultural preservation',
                'community_commitment' => 'Return to community and help preserve culture'
            ]
        ];
    }
}
