<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Scholarship;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('student123'),
            'remember_token' => Str::random(10),
            'role' => 'student', // Default role
            'scholarship_id' => null, // Will be set by states
            'profile_data' => json_encode([
                'contact_number' => '+63 9' . fake()->numberBetween(100000000, 999999999),
                'address' => fake()->address() . ', Daanbantayan, Cebu',
                'date_of_birth' => fake()->dateTimeBetween('-25 years', '-18 years')->format('Y-m-d'),
            ]),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Create a student user
     */
    public function student(?int $scholarshipId = null): static
    {
        return $this->state(function (array $attributes) use ($scholarshipId) {
            $scholarship = $scholarshipId
                ? Scholarship::find($scholarshipId)
                : Scholarship::where('is_active', true)->inRandomOrder()->first();

            if (!$scholarship) {
                throw new \Exception('No scholarship found for student factory');
            }

            $firstName = fake()->firstName();
            $lastName = fake()->lastName();
            $middleInitial = fake()->randomLetter();
            $fullName = $firstName . ' ' . $middleInitial . '. ' . $lastName;

            $baseProfileData = [
                'student_id' => strtoupper(substr($scholarship->slug, 0, 2)) . '-' . fake()->year() . '-' . fake()->randomNumber(3, true),
                'school' => fake()->randomElement([
                    'University of San Carlos',
                    'Cebu Institute of Technology - University',
                    'University of the Philippines Cebu',
                    'Cebu Normal University',
                    'Cebu Technological University',
                    'Southwestern University PHINMA'
                ]),
                'course' => $this->getRandomCourse($scholarship->slug),
                'year_level' => fake()->randomElement(['1st Year', '2nd Year', '3rd Year', '4th Year']),
                'gpa' => $this->generateRealisticGPA($scholarship->slug),
                'contact_number' => '+63 9' . fake()->numberBetween(100000000, 999999999),
                'address' => fake()->address() . ', Daanbantayan, Cebu',
                'parent_guardian' => 'Mr. ' . fake()->name('male') . ' & Mrs. ' . fake()->name('female'),
                'parent_contact' => '+63 9' . fake()->numberBetween(100000000, 999999999),
                'scholarship_program' => $scholarship->name
            ];

            // Add scholarship-specific profile data
            $specificProfileData = $this->getScholarshipSpecificProfile($scholarship->slug);
            $profileData = array_merge($baseProfileData, $specificProfileData);

            return [
                'name' => $fullName,
                'email' => strtolower(str_replace([' ', '.'], ['', ''], $fullName)) . '@student.daanbantayan.edu.ph',
                'role' => 'student',
                'scholarship_id' => $scholarship->id,
                'profile_data' => json_encode($profileData),
            ];
        });
    }

    /**
     * Create a committee member user
     */
    public function committee(?int $scholarshipId = null): static
    {
        return $this->state(function (array $attributes) use ($scholarshipId) {
            $scholarship = $scholarshipId
                ? Scholarship::find($scholarshipId)
                : Scholarship::where('is_active', true)->inRandomOrder()->first();

            if (!$scholarship) {
                throw new \Exception("No scholarship found for committee factory");
            }

            $name = fake()->name();
            $email = strtolower(str_replace([' ', '.'], ['', ''], $name)) . '@daanbantayan.gov.ph';

            return [
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('committee123'),
                'role' => 'committee',
                'scholarship_id' => $scholarshipId,
                'profile_data' => json_encode([
                    'position' => 'Committee Member - ' . $scholarship->name,
                    'department' => $this->getDepartmentByScholarship($scholarship->slug),
                    'contact_number' => '+63 9' . fake()->numberBetween(100000000, 999999999),
                    'employee_id' => strtoupper(substr($scholarship->slug, 0, 3)) . '-2024-' . fake()->randomNumber(3, true),
                    'expertise' => $this->getExpertiseByScholarship($scholarship->slug),
                    'education' => $this->getEducationByScholarship($scholarship->slug),
                    'experience_years' => fake()->numberBetween(3, 15)
                ])
            ];
        });
    }

    /**
     * Create an admin user
     */
    public function admin(): static
    {
        return $this->state(function (array $attributes) {
            $name = fake()->name();

            return [
                'name' => $name,
                'email' => strtolower(str_replace([' ', '.'], ['', ''], $name)) . '@daanbantayan.gov.ph',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'scholarship_id' => null, // Admin has no scholarship affiliation
                'profile_data' => json_encode([
                    'position' => 'System Administrator',
                    'department' => 'Municipal IT Office',
                    'employee_id' => 'ADMIN-2024-' . fake()->randomNumber(3, true),
                    'contact_number' => '+63 9' . fake()->numberBetween(100000000, 999999999),
                    'access_level' => 'full_system_access',
                    'responsibilities' => [
                        'System maintenance',
                        'User management',
                        'Data backup and security',
                        'Technical support'
                    ]
                ])
            ];
        });
    }

    /**
     * Get random course based on scholarship slug
     */
    private function getRandomCourse(string $scholarshipSlug): string
    {
        return match ($scholarshipSlug) {
            'merit-scholarship' => fake()->randomElement([
                'BS Computer Science',
                'BS Information Technology',
                'BS Civil Engineering',
                'BS Mechanical Engineering',
                'BS Electrical Engineering',
                'BS Mathematics',
                'BS Physics',
                'BS Chemistry',
                'BS Accountancy',
                'BS Business Administration'
            ]),
            'sports-scholarship' => fake()->randomElement([
                'BS Physical Education',
                'BS Sports Science',
                'BS Kinesiology',
                'BS Exercise and Sports Sciences',
                'BS Recreation and Leisure Studies',
                'BS Tourism Management',
                'BS Business Administration',
                'BS Psychology'
            ]),
            'need-based-scholarship' => fake()->randomElement([
                'BS Elementary Education',
                'BS Secondary Education',
                'BS Special Needs Education',
                'BS Social Work',
                'BS Nursing',
                'BS Public Health',
                'BS Agriculture',
                'BS Information Technology',
                'BS Business Administration',
                'BS Tourism Management'
            ]),
            'indigenous-scholarship' => fake()->randomElement([
                'BS Anthropology',
                'BS Sociology',
                'BS Environmental Science',
                'BS Agriculture',
                'BS Forestry',
                'BS Social Work',
                'BS Education',
                'BS Development Communication',
                'BS Public Administration',
                'AB History'
            ]),
            default => fake()->randomElement([
                'BS Business Administration',
                'BS Information Technology',
                'BS Education'
            ])
        };
    }

    /**
     * Generate realistic GPA based on scholarship type
     */
    private function generateRealisticGPA(string $scholarshipSlug): float
    {
        return match ($scholarshipSlug) {
            'merit-scholarship' => fake()->randomFloat(2, 3.5, 4.0), // High GPA for merit
            'sports-scholarship' => fake()->randomFloat(2, 2.75, 3.5), // Moderate GPA for sports
            'need-based-scholarship' => fake()->randomFloat(2, 2.5, 3.8), // Variable GPA
            'indigenous-scholarship' => fake()->randomFloat(2, 2.5, 3.6), // Variable GPA
            default => fake()->randomFloat(2, 2.0, 4.0)
        };
    }

    /**
     * Get scholarship-specific profile data
     */
    private function getScholarshipSpecificProfile(string $scholarshipSlug): array
    {
        return match ($scholarshipSlug) {
            'merit-scholarship' => [
                'class_rank' => fake()->numberBetween(1, 15) . ' of ' . fake()->numberBetween(80, 200),
                'academic_awards' => fake()->randomElements([
                    'Dean\'s List',
                    'Academic Excellence Award',
                    'Honor Student',
                    'Outstanding Student Award',
                    'Magna Cum Laude',
                    'Academic Scholarship Recipient'
                ], fake()->numberBetween(1, 3)),
                'extracurricular_activities' => fake()->randomElements([
                    'Student Government Officer',
                    'Honor Society Member',
                    'Academic Club President',
                    'Research Assistant',
                    'Debate Team Captain',
                    'Math Club Member'
                ], fake()->numberBetween(1, 2)),
                'leadership_roles' => fake()->randomElements([
                    'Class President',
                    'Organization Vice President',
                    'Project Coordinator',
                    'Student Council Member'
                ], fake()->numberBetween(0, 2))
            ],
            'sports-scholarship' => [
                'primary_sport' => fake()->randomElement(['Basketball', 'Volleyball', 'Swimming', 'Track and Field', 'Football', 'Badminton', 'Table Tennis']),
                'secondary_sports' => fake()->randomElements(['Basketball', 'Volleyball', 'Swimming', 'Track and Field', 'Badminton'], fake()->numberBetween(0, 2)),
                'position' => fake()->randomElement(['Forward', 'Guard', 'Center', 'Midfielder', 'Defender', 'Captain', 'Setter', 'Spiker', 'Libero']),
                'years_experience' => fake()->numberBetween(3, 12),
                'team_affiliation' => fake()->randomElement(['School Varsity Team', 'Provincial Team', 'Regional Team', 'Club Team']),
                'achievements' => fake()->randomElements([
                    'Regional Champion',
                    'MVP Award',
                    'Best Athlete',
                    'Team Captain',
                    'Provincial Qualifier',
                    'Inter-school Champion',
                    'Sports Festival Gold Medalist'
                ], fake()->numberBetween(1, 3)),
                'training_hours_weekly' => fake()->numberBetween(15, 30),
                'fitness_level' => fake()->randomElement(['Excellent', 'Very Good', 'Good']),
                'coach_name' => fake()->name(),
                'coach_contact' => '+63 9' . fake()->numberBetween(100000000, 999999999)
            ],
            'need-based-scholarship' => [
                'family_monthly_income' => '₱' . fake()->numberBetween(8, 35) . ',000',
                'family_size' => fake()->numberBetween(4, 8),
                'siblings_count' => fake()->numberBetween(2, 6),
                'siblings_in_school' => fake()->numberBetween(1, 4),
                'father_name' => fake()->name('male'),
                'father_occupation' => fake()->randomElement(['Farmer', 'Fisherman', 'Tricycle Driver', 'Construction Worker', 'Security Guard', 'Unemployed', 'OFW']),
                'father_income' => '₱' . fake()->numberBetween(5, 20) . ',000',
                'mother_name' => fake()->name('female'),
                'mother_occupation' => fake()->randomElement(['Housewife', 'Market Vendor', 'Domestic Helper', 'Seamstress', 'Laundrywoman', 'OFW']),
                'mother_income' => '₱' . fake()->numberBetween(3, 15) . ',000',
                'housing_type' => fake()->randomElement(['Own house', 'Rented house', 'Living with relatives', 'Informal settlement']),
                'financial_difficulties' => fake()->randomElements([
                    'Insufficient family income',
                    'Parent unemployment',
                    'Medical expenses',
                    'Multiple siblings in school',
                    'Natural disaster impact',
                    'Single parent household'
                ], fake()->numberBetween(1, 3)),
                'part_time_work' => fake()->boolean(40), // 40% chance of having part-time work
                'work_description' => fake()->optional()->randomElement(['Computer shop assistant', 'Tutor', 'Sales assistant', 'Food service', 'Delivery rider'])
            ],
            'indigenous-scholarship' => [
                'indigenous_group' => fake()->randomElement([
                    'Cebuano Indigenous Community',
                    'Bisaya Tribal Group',
                    'Local Ancestral Domain',
                    'Traditional Fishing Community',
                    'Mountain Tribe Descendant'
                ]),
                'tribal_affiliation' => 'Recognized Indigenous Community Member',
                'cultural_role' => fake()->randomElement([
                    'Cultural Performer',
                    'Traditional Dance Leader',
                    'Language Keeper',
                    'Traditional Craft Maker',
                    'Community Youth Leader',
                    'Cultural Ambassador'
                ]),
                'cultural_practices' => fake()->randomElements([
                    'Traditional farming methods',
                    'Indigenous language preservation',
                    'Cultural dance and music',
                    'Traditional crafts and weaving',
                    'Ancestral domain stewardship',
                    'Community rituals and ceremonies',
                    'Traditional medicine practices'
                ], fake()->numberBetween(2, 4)),
                'language_spoken' => fake()->randomElement([
                    'Cebuano and indigenous dialect',
                    'Bisayan and tribal language',
                    'Local indigenous language',
                    'Traditional community dialect'
                ]),
                'community_location' => fake()->randomElement([
                    'Indigenous ancestral domain in Daanbantayan',
                    'Traditional community in mountain area',
                    'Coastal indigenous settlement',
                    'Ancestral land in rural barangay'
                ]),
                'family_cultural_role' => fake()->randomElement([
                    'Traditional healers',
                    'Community elders',
                    'Cultural knowledge keepers',
                    'Ancestral domain caretakers',
                    'Traditional craft makers'
                ]),
                'preservation_activities' => fake()->randomElements([
                    'Teaching traditional dances',
                    'Documenting indigenous stories',
                    'Organizing cultural festivals',
                    'Youth cultural education',
                    'Traditional craft workshops'
                ], fake()->numberBetween(1, 3))
            ],
            default => []
        };
    }

    /**
     * Get department by scholarship type
     */
    private function getDepartmentByScholarship(string $scholarshipSlug): string
    {
        return match ($scholarshipSlug) {
            'merit-scholarship' => 'Municipal Education Office',
            'sports-scholarship' => 'Municipal Sports Development Office',
            'need-based-scholarship' => 'Municipal Social Welfare and Development Office',
            'indigenous-scholarship' => 'Municipal Indigenous Peoples Affairs Office',
            default => 'Municipal Office'
        };
    }

    /**
     * Get expertise by scholarship type
     */
    private function getExpertiseByScholarship(string $scholarshipSlug): string
    {
        return match ($scholarshipSlug) {
            'merit-scholarship' => 'Academic Excellence Evaluation and Educational Assessment',
            'sports-scholarship' => 'Athletic Performance Assessment and Sports Development',
            'need-based-scholarship' => 'Socio-economic Assessment and Financial Need Evaluation',
            'indigenous-scholarship' => 'Cultural Preservation and Indigenous Community Affairs',
            default => 'General Program Assessment'
        };
    }

    /**
     * Get education background by scholarship type
     */
    private function getEducationByScholarship(string $scholarshipSlug): string
    {
        return match ($scholarshipSlug) {
            'merit-scholarship' => fake()->randomElement([
                'PhD in Education Administration',
                'Master in Educational Leadership',
                'Master in Curriculum and Instruction',
                'PhD in Educational Management'
            ]),
            'sports-scholarship' => fake()->randomElement([
                'Master in Sports Science',
                'Bachelor in Physical Education with Sports Management',
                'Master in Sports Psychology',
                'Diploma in Sports Coaching'
            ]),
            'need-based-scholarship' => fake()->randomElement([
                'Master in Social Work',
                'Master in Public Administration',
                'Master in Community Development',
                'Bachelor in Social Work with specialization'
            ]),
            'indigenous-scholarship' => fake()->randomElement([
                'Master in Anthropology',
                'Master in Cultural Studies',
                'Bachelor in Indigenous Studies',
                'Master in Community Development'
            ]),
            default => 'Bachelor\'s Degree with relevant experience'
        };
    }
}
