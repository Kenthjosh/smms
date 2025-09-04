<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Scholarship;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Scholarship>
 */
class ScholarshipFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->randomElement([
            'Academic Excellence Scholarship',
            'Leadership Development Grant',
            'Community Service Award',
            'Innovation and Technology Scholarship',
            'Environmental Stewardship Grant',
            'Arts and Culture Scholarship',
            'STEM Education Grant',
            'Rural Development Scholarship'
        ]);

        $slug = Str::slug($name);

        return [
            'name' => $name,
            'slug' => $slug,
            'description' => fake()->paragraph(3),
            'eligibility_criteria' => json_encode($this->generateEligibilityCriteria()),
            'required_documents' => json_encode($this->generateRequiredDocuments()),
            'selection_criteria' => json_encode($this->generateSelectionCriteria()),
            'benefits' => json_encode($this->generateBenefits()),
            'requirements' => json_encode($this->generateRequirements()),
            'form_schema' => json_encode($this->generateFormSchema()),
            'table_config' => json_encode($this->generateTableConfig()),
            'infolist_config' => json_encode($this->generateInfolistConfig()),
            'settings' => json_encode($this->generateSettings()),
            'is_active' => fake()->boolean(85), // 85% chance of being active
            'application_deadline' => fake()->dateTimeBetween('+30 days', '+180 days'),
            'created_at' => fake()->dateTimeBetween('-2 years', '-6 months'),
            'updated_at' => function (array $attributes) {
                return fake()->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    /**
     * Merit-based scholarship
     */
    public function merit(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Merit-Based Academic Scholarship',
                'slug' => 'merit-scholarship',
                'description' => 'A scholarship program designed to recognize and support academically excellent students who have demonstrated outstanding performance in their studies and show potential for continued academic success.',
                'eligibility_criteria' => json_encode([
                    'minimum_gpa' => 3.5,
                    'class_rank' => 'Top 10% of class',
                    'academic_performance' => 'Consistent honor roll or dean\'s list',
                    'enrollment_status' => 'Full-time undergraduate student',
                    'residency' => 'Legal resident of Daanbantayan, Cebu',
                    'age_requirement' => '18-25 years old'
                ]),
                'required_documents' => json_encode([
                    'transcript_of_records',
                    'certificate_of_enrollment',
                    'birth_certificate',
                    'barangay_clearance',
                    'passport_photo',
                    'academic_recommendation_letter',
                    'certificate_of_awards',
                    'essay_document'
                ]),
                'selection_criteria' => json_encode([
                    'academic_performance' => ['weight' => 40, 'description' => 'GPA and class ranking'],
                    'achievements' => ['weight' => 25, 'description' => 'Academic awards and recognitions'],
                    'leadership' => ['weight' => 20, 'description' => 'Leadership roles and activities'],
                    'community_involvement' => ['weight' => 15, 'description' => 'Community service and involvement']
                ]),
                'benefits' => json_encode([
                    'tuition_coverage' => 'Full tuition fee coverage',
                    'book_allowance' => '₱5,000 per semester',
                    'monthly_stipend' => '₱3,000 per month',
                    'research_support' => 'Up to ₱10,000 for thesis/research projects',
                    'mentorship_program' => 'Access to academic mentorship',
                    'scholarship_duration' => 'Up to 4 years (undergraduate)'
                ])
            ];
        });
    }

    /**
     * Sports scholarship
     */
    public function sports(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Athletic Excellence Scholarship',
                'slug' => 'sports-scholarship',
                'description' => 'Supporting student-athletes who excel in sports while maintaining academic standards. This program aims to develop well-rounded individuals who can represent Daanbantayan in various athletic competitions.',
                'eligibility_criteria' => json_encode([
                    'minimum_gpa' => 2.75,
                    'athletic_experience' => 'Minimum 2 years participation in organized sports',
                    'sports_categories' => [
                        'Basketball',
                        'Volleyball',
                        'Swimming',
                        'Track and Field',
                        'Football',
                        'Badminton',
                        'Table Tennis',
                        'Baseball'
                    ],
                    'medical_clearance' => 'Must pass medical examination for sports participation',
                    'age_requirement' => '16-23 years old'
                ]),
                'required_documents' => json_encode([
                    'transcript_of_records',
                    'birth_certificate',
                    'barangay_clearance',
                    'medical_certificate',
                    'passport_photo',
                    'athletic_performance_record',
                    'coach_recommendation_letter',
                    'sports_participation_certificate'
                ]),
                'benefits' => json_encode([
                    'tuition_coverage' => 'Full or partial tuition support',
                    'training_allowance' => '₱2,500 per month',
                    'equipment_support' => 'Sports equipment and gear',
                    'competition_support' => 'Travel and accommodation for competitions',
                    'medical_support' => 'Sports-related medical coverage',
                    'coaching_access' => 'Professional coaching and training'
                ])
            ];
        });
    }

    /**
     * Need-based scholarship
     */
    public function needBased(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Financial Need Scholarship',
                'slug' => 'need-based-scholarship',
                'description' => 'Providing educational opportunities for economically disadvantaged students who demonstrate financial need but show academic potential and commitment to their education.',
                'eligibility_criteria' => json_encode([
                    'family_income' => 'Maximum family income of ₱50,000 per month',
                    'minimum_gpa' => 2.5,
                    'financial_status' => 'Must demonstrate significant financial need',
                    'family_size' => 'Consideration given to large families',
                    'employment_status' => 'Parent/guardian employment situation considered',
                    'housing_situation' => 'Housing and living conditions assessment'
                ]),
                'required_documents' => json_encode([
                    'transcript_of_records',
                    'birth_certificate',
                    'barangay_clearance',
                    'certificate_of_indigency',
                    'family_income_certificate',
                    'passport_photo',
                    'parent_employment_certificate',
                    'utility_bills'
                ]),
                'benefits' => json_encode([
                    'tuition_coverage' => 'Full tuition fee support',
                    'book_allowance' => '₱3,000 per semester',
                    'monthly_allowance' => '₱2,000 per month',
                    'transportation_allowance' => '₱1,000 per month',
                    'emergency_fund' => 'Access to emergency financial assistance',
                    'career_counseling' => 'Career guidance and job placement assistance'
                ])
            ];
        });
    }

    /**
     * Indigenous scholarship
     */
    public function indigenous(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Indigenous Peoples Education Scholarship',
                'slug' => 'indigenous-scholarship',
                'description' => 'Supporting indigenous students in accessing quality education while promoting cultural preservation and community development within indigenous communities of Daanbantayan.',
                'eligibility_criteria' => json_encode([
                    'indigenous_status' => 'Must be a member of recognized indigenous community',
                    'cultural_involvement' => 'Active participation in cultural activities',
                    'community_endorsement' => 'Endorsement from tribal leaders or indigenous organization',
                    'minimum_gpa' => 2.5,
                    'commitment_to_community' => 'Commitment to serve indigenous community after graduation'
                ]),
                'required_documents' => json_encode([
                    'transcript_of_records',
                    'birth_certificate',
                    'certificate_of_tribal_membership',
                    'barangay_clearance',
                    'cultural_endorsement_letter',
                    'passport_photo',
                    'community_recommendation_letter',
                    'ancestral_domain_certificate'
                ]),
                'benefits' => json_encode([
                    'tuition_coverage' => 'Full tuition and fees',
                    'cultural_allowance' => '₱2,500 per month',
                    'research_support' => 'Support for cultural research projects',
                    'mentorship_program' => 'Cultural mentorship and guidance',
                    'community_project_fund' => 'Funding for community development projects',
                    'cultural_preservation_support' => 'Support for cultural preservation activities'
                ])
            ];
        });
    }

    /**
     * Active scholarship
     */
    public function active(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => true,
                'application_deadline' => fake()->dateTimeBetween('+30 days', '+120 days'),
            ];
        });
    }

    /**
     * Inactive scholarship
     */
    public function inactive(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
                'application_deadline' => fake()->dateTimeBetween('-365 days', '-30 days'),
            ];
        });
    }

    /**
     * Generate eligibility criteria
     */
    private function generateEligibilityCriteria(): array
    {
        return [
            'minimum_gpa' => fake()->randomFloat(1, 2.0, 3.5),
            'age_requirement' => fake()->randomElement(['18-25 years', '16-23 years', '18-30 years']),
            'residency' => 'Legal resident of Daanbantayan, Cebu',
            'enrollment_status' => fake()->randomElement(['Full-time', 'Part-time accepted', 'Full-time preferred']),
            'academic_standing' => fake()->randomElement(['Good standing', 'No academic probation', 'Satisfactory progress']),
            'additional_requirements' => fake()->randomElements([
                'Financial need assessment',
                'Community service involvement',
                'Leadership experience',
                'Extracurricular participation'
            ], fake()->numberBetween(1, 3))
        ];
    }

    /**
     * Generate required documents
     */
    private function generateRequiredDocuments(): array
    {
        $basicDocs = [
            'transcript_of_records',
            'birth_certificate',
            'barangay_clearance',
            'passport_photo'
        ];

        $additionalDocs = [
            'certificate_of_enrollment',
            'recommendation_letter',
            'essay_document',
            'medical_certificate',
            'certificate_of_indigency',
            'parent_employment_certificate'
        ];

        return array_merge($basicDocs, fake()->randomElements($additionalDocs, fake()->numberBetween(2, 4)));
    }

    /**
     * Generate selection criteria
     */
    private function generateSelectionCriteria(): array
    {
        return [
            'academic_performance' => [
                'weight' => fake()->numberBetween(30, 50),
                'description' => 'GPA, class ranking, and academic achievements'
            ],
            'financial_need' => [
                'weight' => fake()->numberBetween(20, 40),
                'description' => 'Family income and financial circumstances'
            ],
            'community_involvement' => [
                'weight' => fake()->numberBetween(10, 25),
                'description' => 'Community service and civic engagement'
            ],
            'leadership_potential' => [
                'weight' => fake()->numberBetween(10, 20),
                'description' => 'Leadership roles and potential'
            ],
            'essay_quality' => [
                'weight' => fake()->numberBetween(5, 15),
                'description' => 'Personal statement and essay responses'
            ]
        ];
    }

    /**
     * Generate scholarship benefits
     */
    private function generateBenefits(): array
    {
        return [
            'tuition_coverage' => fake()->randomElement([
                'Full tuition fee coverage',
                'Partial tuition support (50-80%)',
                'Up to ₱50,000 per year'
            ]),
            'monthly_allowance' => '₱' . fake()->numberBetween(2000, 5000) . ' per month',
            'book_allowance' => '₱' . fake()->numberBetween(2000, 5000) . ' per semester',
            'other_benefits' => fake()->randomElements([
                'Transportation allowance',
                'Meal allowance',
                'Research support',
                'Internship opportunities',
                'Career counseling',
                'Mentorship program'
            ], fake()->numberBetween(2, 4)),
            'scholarship_duration' => fake()->randomElement([
                'Up to 4 years (undergraduate)',
                'One academic year (renewable)',
                'Until graduation (with satisfactory progress)'
            ])
        ];
    }

    /**
     * Generate scholarship requirements
     */
    private function generateRequirements(): array
    {
        return [
            'academic_requirements' => [
                'maintain_gpa' => fake()->randomFloat(1, 2.5, 3.0),
                'credit_load' => fake()->randomElement(['Minimum 15 units per semester', 'Full-time enrollment']),
                'progress_requirement' => 'Satisfactory academic progress'
            ],
            'reporting_requirements' => [
                'grade_submission' => 'Submit grades each semester',
                'progress_report' => 'Quarterly progress reports',
                'community_service' => fake()->numberBetween(20, 40) . ' hours per semester'
            ],
            'conduct_requirements' => [
                'disciplinary_record' => 'Maintain good disciplinary standing',
                'community_involvement' => 'Active participation in community activities',
                'representation' => 'Represent scholarship program positively'
            ]
        ];
    }

    /**
     * Generate form schema for dynamic forms
     */
    private function generateFormSchema(): array
    {
        return [
            'sections' => [
                [
                    'title' => 'Personal Information',
                    'fields' => [
                        ['name' => 'full_name', 'type' => 'text', 'required' => true],
                        ['name' => 'date_of_birth', 'type' => 'date', 'required' => true],
                        ['name' => 'gender', 'type' => 'select', 'options' => ['Male', 'Female'], 'required' => true],
                        ['name' => 'contact_number', 'type' => 'text', 'required' => true],
                        ['name' => 'email', 'type' => 'email', 'required' => true]
                    ]
                ],
                [
                    'title' => 'Academic Information',
                    'fields' => [
                        ['name' => 'school', 'type' => 'text', 'required' => true],
                        ['name' => 'course', 'type' => 'text', 'required' => true],
                        ['name' => 'year_level', 'type' => 'select', 'options' => ['1st Year', '2nd Year', '3rd Year', '4th Year'], 'required' => true],
                        ['name' => 'gpa', 'type' => 'number', 'required' => true]
                    ]
                ],
                [
                    'title' => 'Essay Questions',
                    'fields' => [
                        ['name' => 'career_goals', 'type' => 'textarea', 'required' => true],
                        ['name' => 'why_deserving', 'type' => 'textarea', 'required' => true]
                    ]
                ]
            ]
        ];
    }

    /**
     * Generate table configuration
     */
    private function generateTableConfig(): array
    {
        return [
            'columns' => [
                ['name' => 'student_name', 'label' => 'Student Name', 'sortable' => true],
                ['name' => 'school', 'label' => 'School', 'sortable' => true],
                ['name' => 'gpa', 'label' => 'GPA', 'sortable' => true],
                ['name' => 'status', 'label' => 'Status', 'sortable' => true, 'filterable' => true],
                ['name' => 'submitted_at', 'label' => 'Date Submitted', 'sortable' => true]
            ],
            'filters' => [
                'status' => ['draft', 'submitted', 'under_review', 'approved', 'rejected'],
                'school' => ['University of San Carlos', 'CIT-U', 'UP Cebu'],
                'year_level' => ['1st Year', '2nd Year', '3rd Year', '4th Year']
            ],
            'actions' => ['view', 'edit', 'approve', 'reject']
        ];
    }

    /**
     * Generate infolist configuration
     */
    private function generateInfolistConfig(): array
    {
        return [
            'sections' => [
                [
                    'title' => 'Student Information',
                    'fields' => ['full_name', 'date_of_birth', 'contact_number', 'email']
                ],
                [
                    'title' => 'Academic Details',
                    'fields' => ['school', 'course', 'year_level', 'gpa']
                ],
                [
                    'title' => 'Application Status',
                    'fields' => ['status', 'submitted_at', 'reviewed_at', 'committee_notes']
                ]
            ]
        ];
    }

    /**
     * Generate general settings
     */
    private function generateSettings(): array
    {
        return [
            'application_settings' => [
                'max_applications_per_user' => 1,
                'allow_draft_save' => true,
                'auto_submit_on_deadline' => false,
                'email_notifications' => true
            ],
            'review_settings' => [
                'require_committee_consensus' => fake()->boolean(),
                'minimum_reviewers' => fake()->numberBetween(1, 3),
                'review_deadline_days' => fake()->numberBetween(14, 30)
            ],
            'notification_settings' => [
                'send_confirmation_email' => true,
                'send_status_updates' => true,
                'reminder_days_before_deadline' => [7, 3, 1]
            ]
        ];
    }
}
