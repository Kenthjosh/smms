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
            'status' => fake()->randomElement(['draft', 'submitted', 'under_review', 'approved', 'rejected']),
            'committee_notes' => null,
            'submitted_at' => null,
            'reviewed_at' => null,
            'reviewed_by' => null,
            'created_at' => fake()->dateTimeBetween('-60 days', '-1 days'),
            'updated_at' => function (array $attributes) {
                return fake()->dateTimeBetween($attributes['created_at'], 'now');
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
     * Application with submitted status
     */
    public function submitted(): static
    {
        return $this->state(function (array $attributes) {
            $submittedAt = fake()->dateTimeBetween('-30 days', '-7 days');

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
            $submittedAt = fake()->dateTimeBetween('-20 days', '-10 days');

            return [
                'status' => 'under_review',
                'submitted_at' => $submittedAt,
                'committee_notes' => fake()->randomElement([
                    'Application is complete and under committee review.',
                    'Reviewing academic credentials and supporting documents.',
                    'Awaiting additional verification from school.',
                    'Committee meeting scheduled for final decision.',
                    'Currently evaluating against program criteria.'
                ]),
                'updated_at' => fake()->dateTimeBetween($submittedAt, 'now'),
            ];
        });
    }

    /**
     * Approved application
     */
    public function approved(): static
    {
        return $this->state(function (array $attributes) {
            $submittedAt = fake()->dateTimeBetween('-45 days', '-20 days');
            $reviewedAt = fake()->dateTimeBetween($submittedAt, '-5 days');

            return [
                'status' => 'approved',
                'submitted_at' => $submittedAt,
                'reviewed_at' => $reviewedAt,
                'committee_notes' => fake()->randomElement([
                    'Application approved. Excellent academic performance and strong community involvement.',
                    'Committee unanimously approved this outstanding application.',
                    'Meets all requirements with exceptional qualifications.',
                    'Approved for full scholarship benefits. Congratulations!',
                    'Exceptional candidate who demonstrates all required criteria.',
                    'Strong academic record and compelling personal statement.'
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
            $submittedAt = fake()->dateTimeBetween('-30 days', '-15 days');
            $reviewedAt = fake()->dateTimeBetween($submittedAt, '-3 days');

            return [
                'status' => 'rejected',
                'submitted_at' => $submittedAt,
                'reviewed_at' => $reviewedAt,
                'committee_notes' => fake()->randomElement([
                    'Application does not meet minimum GPA requirements.',
                    'Incomplete documentation submitted.',
                    'Financial need criteria not sufficiently demonstrated.',
                    'Application submitted after deadline.',
                    'Required documents missing or invalid.',
                    'Does not meet specific program eligibility criteria.'
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
                'application_data' => $this->generateIncompleteApplicationData(),
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
            default => $this->generateDefaultData($user)
        };

        return json_encode($data);
    }

    /**
     * Generate Merit scholarship application data
     */
    private function generateMeritData(User $user): array
    {
        return [
            'personal_information' => [
                'full_name' => $user->name,
                'date_of_birth' => fake()->date('Y-m-d', '2005-01-01'),
                'place_of_birth' => 'Daanbantayan, Cebu',
                'gender' => fake()->randomElement(['Male', 'Female']),
                'civil_status' => 'Single',
                'nationality' => 'Filipino',
                'contact_number' => '+63 9' . fake()->numberBetween(100000000, 999999999),
                'email_address' => $user->email,
                'permanent_address' => fake()->address() . ', Daanbantayan, Cebu'
            ],
            'academic_information' => [
                'current_gpa' => fake()->numberBetween(350, 400) / 100, // 3.50 to 4.00 for merit
                'class_rank' => fake()->numberBetween(1, 10) . '/' . fake()->numberBetween(50, 200),
                'school' => fake()->randomElement([
                    'University of San Carlos',
                    'Cebu Institute of Technology',
                    'University of the Philippines Cebu',
                    'Cebu Normal University'
                ]),
                'course' => fake()->randomElement([
                    'BS Computer Science',
                    'BS Information Technology',
                    'BS Engineering',
                    'BS Mathematics',
                    'BS Physics'
                ]),
                'year_level' => fake()->randomElement(['1st Year', '2nd Year', '3rd Year', '4th Year']),
                'expected_graduation' => fake()->date('Y-m-d', '+3 years')
            ],
            'achievements' => [
                'academic_awards' => fake()->randomElements([
                    'Dean\'s List - Semester 1',
                    'Academic Excellence Award',
                    'Outstanding Student in Mathematics',
                    'Best Thesis Award',
                    'Magna Cum Laude',
                    'Summa Cum Laude',
                    'President\'s List'
                ], fake()->numberBetween(1, 4)),
                'extracurricular_activities' => fake()->randomElements([
                    'Student Government Secretary',
                    'Computer Science Society Member',
                    'Debate Club President',
                    'Math Olympiad Participant',
                    'Research Assistant',
                    'Academic Club Officer'
                ], fake()->numberBetween(1, 3)),
                'leadership_roles' => fake()->randomElements([
                    'Class President',
                    'Organization Vice President',
                    'Event Coordinator',
                    'Student Council Member'
                ], fake()->numberBetween(0, 2))
            ],
            'essay_responses' => [
                'career_goals' => 'I aspire to become a software engineer and contribute to the technological advancement of our community through innovative solutions.',
                'community_involvement' => 'I have actively volunteered in local literacy programs and organized coding workshops for high school students in our municipality.',
                'why_deserving' => 'My consistent academic performance, leadership experience, and commitment to community service demonstrate my dedication to excellence and social responsibility.',
                'future_plans' => 'After graduation, I plan to work in the technology sector while establishing programs to bridge the digital divide in rural communities like Daanbantayan.'
            ]
        ];
    }

    /**
     * Generate Sports scholarship application data
     */
    private function generateSportsData(User $user): array
    {
        return [
            'personal_information' => [
                'full_name' => $user->name,
                'date_of_birth' => fake()->date('Y-m-d', '2005-01-01'),
                'place_of_birth' => 'Daanbantayan, Cebu',
                'gender' => fake()->randomElement(['Male', 'Female']),
                'civil_status' => 'Single',
                'nationality' => 'Filipino',
                'contact_number' => '+63 9' . fake()->numberBetween(100000000, 999999999),
                'height' => fake()->numberBetween(150, 190) . ' cm',
                'weight' => fake()->numberBetween(45, 85) . ' kg',
                'emergency_contact' => fake()->name(),
                'emergency_phone' => '+63 9' . fake()->numberBetween(100000000, 999999999)
            ],
            'academic_information' => [
                'current_gpa' => fake()->numberBetween(275, 350) / 100, // 2.75 to 3.50 for sports
                'school' => fake()->randomElement([
                    'University of San Carlos',
                    'Cebu Institute of Technology',
                    'University of the Philippines Cebu'
                ]),
                'course' => fake()->randomElement([
                    'BS Physical Education',
                    'BS Sports Science',
                    'BS Kinesiology',
                    'BS Recreation',
                    'BS Business Administration'
                ]),
                'year_level' => fake()->randomElement(['1st Year', '2nd Year', '3rd Year', '4th Year'])
            ],
            'sports_information' => [
                'primary_sport' => fake()->randomElement([
                    'Basketball',
                    'Volleyball',
                    'Football',
                    'Swimming',
                    'Track and Field',
                    'Badminton',
                    'Table Tennis',
                    'Baseball'
                ]),
                'secondary_sports' => fake()->randomElements([
                    'Basketball',
                    'Volleyball',
                    'Swimming',
                    'Tennis',
                    'Badminton'
                ], fake()->numberBetween(0, 2)),
                'years_experience' => fake()->numberBetween(3, 12) . ' years',
                'position_played' => fake()->randomElement([
                    'Point Guard',
                    'Forward',
                    'Center',
                    'Midfielder',
                    'Goalkeeper',
                    'Libero',
                    'Spiker',
                    'Setter'
                ]),
                'team_affiliation' => fake()->randomElement([
                    'School Varsity Team',
                    'Municipal Team',
                    'Provincial Team',
                    'Regional Squad'
                ]),
                'coach_name' => fake()->name(),
                'coach_contact' => '+63 9' . fake()->numberBetween(100000000, 999999999)
            ],
            'achievements' => [
                'competitions' => fake()->randomElements([
                    'Regional Championships - Gold Medal',
                    'Inter-School Sports Fest - MVP',
                    'Provincial Meet - Silver Medal',
                    'National Youth Games - Participant',
                    'CESAFI Tournament - Champion',
                    'Palaro - Regional Qualifier'
                ], fake()->numberBetween(1, 4)),
                'awards' => fake()->randomElements([
                    'Most Valuable Player',
                    'Best Rookie Award',
                    'Sportsmanship Award',
                    'Team Captain',
                    'Outstanding Athlete',
                    'Best Offensive Player'
                ], fake()->numberBetween(1, 3)),
                'records_held' => fake()->randomElements([
                    'School Record in 100m Sprint',
                    'Highest Scoring Average',
                    'Most Assists in a Season',
                    'Best Free Throw Percentage'
                ], fake()->numberBetween(0, 2))
            ],
            'training_schedule' => [
                'training_hours_per_week' => fake()->numberBetween(15, 30),
                'training_days' => fake()->randomElements([
                    'Monday',
                    'Tuesday',
                    'Wednesday',
                    'Thursday',
                    'Friday',
                    'Saturday'
                ], fake()->numberBetween(4, 6)),
                'competition_schedule' => 'Weekends and holidays',
                'fitness_level' => fake()->randomElement(['Excellent', 'Very Good', 'Good'])
            ],
            'medical_information' => [
                'medical_clearance' => 'Cleared for athletic participation',
                'injuries_history' => fake()->optional()->randomElement([
                    'Minor ankle sprain (fully recovered)',
                    'Knee injury (rehabilitated)',
                    'No significant injuries'
                ]),
                'current_medications' => fake()->optional()->text(50)
            ]
        ];
    }

    /**
     * Generate Need-based scholarship application data
     */
    private function generateNeedBasedData(User $user): array
    {
        return [
            'personal_information' => [
                'full_name' => $user->name,
                'date_of_birth' => fake()->date('Y-m-d', '2005-01-01'),
                'place_of_birth' => 'Daanbantayan, Cebu',
                'gender' => fake()->randomElement(['Male', 'Female']),
                'civil_status' => 'Single',
                'nationality' => 'Filipino',
                'contact_number' => '+63 9' . fake()->numberBetween(100000000, 999999999),
                'permanent_address' => fake()->address() . ', Daanbantayan, Cebu',
                'current_address' => fake()->address() . ', Daanbantayan, Cebu'
            ],
            'academic_information' => [
                'current_gpa' => fake()->numberBetween(250, 380) / 100, // 2.50 to 3.80
                'school' => fake()->randomElement([
                    'Daanbantayan National High School',
                    'Local Public University',
                    'State University Branch',
                    'Community College'
                ]),
                'course' => fake()->randomElement([
                    'BS Education',
                    'BS Social Work',
                    'BS Agriculture',
                    'BS Nursing',
                    'BS Information Technology',
                    'BS Business Administration'
                ]),
                'year_level' => fake()->randomElement(['1st Year', '2nd Year', '3rd Year', '4th Year'])
            ],
            'family_information' => [
                'father_name' => fake()->name('male'),
                'father_occupation' => fake()->randomElement([
                    'Farmer',
                    'Fisherman',
                    'Tricycle Driver',
                    'Construction Worker',
                    'Security Guard',
                    'Unemployed',
                    'Deceased',
                    'OFW'
                ]),
                'father_monthly_income' => fake()->randomElement([
                    '₱5,000',
                    '₱8,000',
                    '₱12,000',
                    '₱15,000',
                    '₱18,000',
                    'None'
                ]),
                'father_education' => fake()->randomElement([
                    'Elementary Graduate',
                    'High School Graduate',
                    'Some College',
                    'No Formal Education'
                ]),
                'mother_name' => fake()->name('female'),
                'mother_occupation' => fake()->randomElement([
                    'Housewife',
                    'Market Vendor',
                    'Domestic Helper',
                    'Seamstress',
                    'Laundrywoman',
                    'Unemployed',
                    'OFW',
                    'Sari-sari Store Owner'
                ]),
                'mother_monthly_income' => fake()->randomElement([
                    '₱3,000',
                    '₱6,000',
                    '₱10,000',
                    '₱15,000',
                    '₱20,000',
                    'None'
                ]),
                'mother_education' => fake()->randomElement([
                    'Elementary Graduate',
                    'High School Graduate',
                    'Some College',
                    'No Formal Education'
                ]),
                'number_of_siblings' => fake()->numberBetween(2, 7),
                'siblings_in_school' => fake()->numberBetween(1, 4),
                'family_monthly_income' => fake()->randomElement([
                    '₱8,000',
                    '₱12,000',
                    '₱18,000',
                    '₱25,000',
                    '₱30,000',
                    '₱35,000'
                ]),
                'family_size' => fake()->numberBetween(4, 8)
            ],
            'financial_information' => [
                'current_financial_aid' => fake()->randomElement([
                    'None',
                    'Government Scholarship',
                    'School Grant',
                    'Private Scholarship',
                    'Student Loan'
                ]),
                'monthly_expenses' => [
                    'tuition_fees' => '₱' . fake()->numberBetween(5000, 15000),
                    'books_supplies' => '₱' . fake()->numberBetween(1000, 3000),
                    'transportation' => '₱' . fake()->numberBetween(1500, 4000),
                    'food_allowance' => '₱' . fake()->numberBetween(3000, 6000),
                    'miscellaneous' => '₱' . fake()->numberBetween(500, 2000)
                ],
                'source_of_funding' => fake()->randomElements([
                    'Family Income',
                    'Part-time Job',
                    'Loans',
                    'Extended Family Help',
                    'Scholarship'
                ], fake()->numberBetween(1, 3)),
                'financial_difficulties' => fake()->randomElements([
                    'Insufficient family income',
                    'Multiple siblings in school',
                    'Parent unemployment',
                    'Medical expenses',
                    'Natural disaster impact',
                    'Death of breadwinner',
                    'Business closure due to pandemic'
                ], fake()->numberBetween(1, 4)),
                'part_time_work' => fake()->boolean(40), // 40% have part-time work
                'work_description' => fake()->optional()->randomElement([
                    'Computer shop assistant',
                    'Tutor',
                    'Sales assistant',
                    'Food service',
                    'Delivery rider'
                ])
            ],
            'housing_information' => [
                'housing_type' => fake()->randomElement([
                    'Own house',
                    'Rented house',
                    'Living with relatives',
                    'Informal settlement'
                ]),
                'house_condition' => fake()->randomElement([
                    'Good condition',
                    'Needs minor repairs',
                    'Needs major repairs',
                    'Poor condition'
                ]),
                'utilities_available' => fake()->randomElements([
                    'Electricity',
                    'Running water',
                    'Internet connection',
                    'Cable TV'
                ], fake()->numberBetween(1, 4))
            ],
            'essay_responses' => [
                'financial_need_explanation' => 'Our family struggles financially due to limited income sources and multiple dependents. With several siblings still in school, the burden of educational expenses has become overwhelming for our parents.',
                'how_scholarship_helps' => 'This scholarship would significantly reduce our family\'s financial burden and allow me to focus entirely on my studies without worrying about tuition fees and school expenses.',
                'commitment_to_studies' => 'I am committed to maintaining excellent academic performance and actively participating in community service programs to give back to our community.',
                'future_goals' => 'I plan to use my education to improve not only my family\'s situation but also contribute to the development of our local community through my chosen profession.'
            ]
        ];
    }

    /**
     * Generate Indigenous scholarship application data
     */
    private function generateIndigenousData(User $user): array
    {
        return [
            'personal_information' => [
                'full_name' => $user->name,
                'date_of_birth' => fake()->date('Y-m-d', '2005-01-01'),
                'place_of_birth' => 'Daanbantayan, Cebu',
                'gender' => fake()->randomElement(['Male', 'Female']),
                'civil_status' => 'Single',
                'nationality' => 'Filipino',
                'indigenous_group' => fake()->randomElement([
                    'Cebuano Indigenous',
                    'Bisaya Indigenous',
                    'Local Tribal Community',
                    'Ancestral Domain Residents'
                ]),
                'tribal_affiliation' => fake()->randomElement([
                    'Recognized Tribal Member',
                    'Indigenous Cultural Group Member',
                    'Ancestral Domain Descendant'
                ]),
                'contact_number' => '+63 9' . fake()->numberBetween(100000000, 999999999)
            ],
            'academic_information' => [
                'current_gpa' => fake()->numberBetween(250, 370) / 100,
                'school' => fake()->randomElement([
                    'Daanbantayan National High School',
                    'Local Indigenous School',
                    'Community College',
                    'Alternative Learning System'
                ]),
                'course' => fake()->randomElement([
                    'BS Anthropology',
                    'BS Education',
                    'BS Agriculture',
                    'BS Social Work',
                    'BS Environmental Science',
                    'BS Development Communication'
                ]),
                'year_level' => fake()->randomElement(['1st Year', '2nd Year', '3rd Year', '4th Year']),
                'previous_education' => fake()->randomElement([
                    'Indigenous Community School System',
                    'Regular Public School',
                    'Alternative Learning System',
                    'Mixed Indigenous and Formal Education'
                ])
            ],
            'cultural_information' => [
                'cultural_practices' => fake()->randomElements([
                    'Traditional farming methods',
                    'Indigenous language preservation',
                    'Cultural dance and music',
                    'Traditional crafts and weaving',
                    'Ancestral domain stewardship',
                    'Community rituals and ceremonies',
                    'Traditional medicine practices',
                    'Oral tradition storytelling'
                ], fake()->numberBetween(3, 6)),
                'language_spoken' => fake()->randomElement([
                    'Cebuano and indigenous dialect',
                    'Local tribal language',
                    'Bisayan indigenous variant',
                    'Traditional community dialect'
                ]),
                'community_role' => fake()->randomElement([
                    'Youth Cultural Leader',
                    'Traditional Dance Performer',
                    'Language Preservation Advocate',
                    'Community Volunteer',
                    'Cultural Ambassador',
                    'Traditional Craft Practitioner'
                ]),
                'cultural_preservation_activities' => fake()->randomElements([
                    'Teaching traditional dances to younger generation',
                    'Participating in cultural festivals',
                    'Documenting indigenous stories and legends',
                    'Assisting in community cultural events',
                    'Leading cultural workshops',
                    'Preserving traditional songs and music'
                ], fake()->numberBetween(2, 4))
            ],
            'family_background' => [
                'parents_indigenous_status' => 'Both parents are members of indigenous community',
                'family_occupation' => fake()->randomElement([
                    'Subsistence farming',
                    'Traditional fishing',
                    'Handicraft making',
                    'Forest stewardship',
                    'Traditional healing',
                    'Community leadership'
                ]),
                'community_location' => 'Indigenous ancestral domain in Daanbantayan',
                'family_cultural_role' => fake()->randomElement([
                    'Cultural knowledge keepers',
                    'Traditional healers',
                    'Community leaders',
                    'Ancestral domain caretakers',
                    'Ritual specialists',
                    'Cultural educators'
                ]),
                'ancestral_domain_involvement' => fake()->randomElement([
                    'Active in domain protection',
                    'Participates in community decisions',
                    'Helps in cultural preservation',
                    'Involved in traditional governance'
                ])
            ],
            'goals_and_aspirations' => [
                'career_goals' => 'To become an advocate for indigenous rights and cultural preservation while contributing to my community\'s sustainable development.',
                'community_commitment' => 'I plan to return to my community after graduation and use my education to help preserve our culture while improving living conditions and opportunities for indigenous youth.',
                'cultural_preservation_plans' => 'Establish educational programs that combine modern learning with traditional knowledge, and create documentation projects to preserve our cultural heritage for future generations.',
                'advocacy_goals' => 'Work towards greater recognition of indigenous rights and promote policies that protect our ancestral domains and traditional ways of life.'
            ],
            'community_support' => [
                'community_endorsement' => fake()->randomElement([
                    'Endorsed by tribal elders',
                    'Supported by cultural council',
                    'Recommended by community leaders',
                    'Approved by indigenous organization'
                ]),
                'cultural_mentor' => fake()->name(),
                'mentor_role' => fake()->randomElement([
                    'Tribal Elder',
                    'Cultural Leader',
                    'Traditional Healer',
                    'Community Organizer'
                ])
            ]
        ];
    }

    /**
     * Generate default application data (fallback)
     */
    private function generateDefaultData(User $user): array
    {
        return [
            'personal_information' => [
                'full_name' => $user->name,
                'date_of_birth' => fake()->date('Y-m-d', '2005-01-01'),
                'gender' => fake()->randomElement(['Male', 'Female']),
                'civil_status' => 'Single',
                'contact_number' => '+63 9' . fake()->numberBetween(100000000, 999999999),
                'email_address' => $user->email
            ],
            'academic_information' => [
                'current_gpa' => fake()->numberBetween(250, 400) / 100,
                'school' => 'General University',
                'course' => 'General Program',
                'year_level' => fake()->randomElement(['1st Year', '2nd Year', '3rd Year', '4th Year'])
            ]
        ];
    }

    /**
     * Generate basic application data (for definition method)
     */
    private function generateBasicApplicationData(): string
    {
        return json_encode([
            'personal_information' => [
                'full_name' => fake()->name(),
                'date_of_birth' => fake()->date('Y-m-d', '2005-01-01'),
                'gender' => fake()->randomElement(['Male', 'Female']),
                'civil_status' => 'Single',
                'contact_number' => '+63 9' . fake()->numberBetween(100000000, 999999999),
            ],
            'academic_information' => [
                'current_gpa' => fake()->numberBetween(250, 400) / 100,
                'school' => 'General University',
                'course' => 'General Program',
                'year_level' => fake()->randomElement(['1st Year', '2nd Year', '3rd Year', '4th Year'])
            ]
        ]);
    }

    /**
     * Generate incomplete application data for drafts
     */
    private function generateIncompleteApplicationData(): string
    {
        $data = [
            'personal_information' => [
                'full_name' => fake()->name(),
                'date_of_birth' => fake()->optional()->date('Y-m-d', '2005-01-01'),
                'gender' => fake()->optional()->randomElement(['Male', 'Female']),
                'civil_status' => fake()->optional()->randomElement(['Single', 'Married']),
            ],
            'academic_information' => [
                'current_gpa' => fake()->optional()->numberBetween(250, 400) / 100,
                'school' => fake()->optional()->randomElement(['University A', 'College B']),
                'course' => fake()->optional()->text(30),
            ],
            '_completion_status' => [
                'completed_sections' => fake()->randomElements([
                    'personal_information',
                    'academic_information'
                ], fake()->numberBetween(1, 2)),
                'incomplete_sections' => fake()->randomElements([
                    'family_information',
                    'essay_responses',
                    'achievements'
                ], fake()->numberBetween(1, 3)),
                'completion_percentage' => fake()->numberBetween(20, 65),
                'last_saved' => fake()->dateTimeBetween('-30 days', 'now')->format('Y-m-d H:i:s')
            ]
        ];

        return json_encode($data);
    }
}