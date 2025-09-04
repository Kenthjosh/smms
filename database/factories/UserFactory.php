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
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'student', // Default role
            'scholarship_id' => null, // Will be set by states
            'profile_data' => json_encode([
                'contact_number' => fake()->phoneNumber(),
                'address' => fake()->address(),
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

            $profileData = [
                'student_id' => 'ST-' . fake()->year() . '-' . fake()->randomNumber(3, true),
                'school' => fake()->randomElement([
                    'University of San Carlos',
                    'Cebu Institute of Technology',
                    'University of the Philippines Cebu',
                    'Cebu Normal University',
                    'Cebu Technological University'
                ]),
                'course' => $this->getRandomCourse($scholarship?->type),
                'year_level' => fake()->randomElement(['1st Year', '2nd Year', '3rd Year', '4th Year']),
                'gpa' => fake()->randomFloat(2, 2.0, 4.0),
                'contact_number' => fake()->phoneNumber(),
                'address' => fake()->address() . ', Daanbantayan, Cebu',
                'parent_name' => fake()->name() . ' & ' . fake()->name(),
                'parent_contact' => fake()->phoneNumber(),
            ];

            // Add scholarship-specific profile data
            if ($scholarship) {
                $profileData = array_merge($profileData, $this->getScholarshipSpecificProfile($scholarship->type));
            }

            return [
                'role' => 'student',
                'scholarship_id' => $scholarship?->id,
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

            return [
                'role' => 'committee',
                'scholarship_id' => $scholarship?->id,
                'email' => $this->getCommitteeEmail($scholarship?->slug),
                'profile_data' => json_encode([
                    'position' => 'Committee Member - ' . ($scholarship?->name ?? 'General'),
                    'department' => $this->getDepartmentByScholarship($scholarship?->type),
                    'employee_id' => fake()->bothify('EMP-####'),
                    'contact_number' => fake()->phoneNumber(),
                    'expertise' => $this->getExpertiseByScholarship($scholarship?->type),
                ]),
            ];
        });
    }

    /**
     * Create an admin user
     */
    public function admin(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'admin',
            'scholarship_id' => null, // Admin has no scholarship affiliation
            'profile_data' => json_encode([
                'position' => 'System Administrator',
                'department' => 'IT Department',
                'employee_id' => 'ADMIN-' . fake()->randomNumber(4),
                'contact_number' => fake()->phoneNumber(),
                'access_level' => 'super_admin',
            ]),
        ]);
    }

    /**
     * Get random course based on scholarship type
     */
    private function getRandomCourse(?string $scholarshipType): string
    {
        return match ($scholarshipType) {
            'merit' => fake()->randomElement([
                'Bachelor of Science in Computer Science',
                'Bachelor of Science in Information Technology',
                'Bachelor of Science in Engineering',
                'Bachelor of Science in Mathematics',
                'Bachelor of Arts in Communication',
            ]),
            'sports' => fake()->randomElement([
                'Bachelor of Science in Sports Science',
                'Bachelor of Physical Education',
                'Bachelor of Science in Kinesiology',
                'Bachelor of Arts in Recreation',
            ]),
            'need-based' => fake()->randomElement([
                'Bachelor of Science in Nursing',
                'Bachelor of Elementary Education',
                'Bachelor of Science in Social Work',
                'Bachelor of Science in Agriculture',
            ]),
            'indigenous' => fake()->randomElement([
                'Bachelor of Arts in Anthropology',
                'Bachelor of Arts in History',
                'Bachelor of Arts in Filipino',
                'Bachelor of Science in Environmental Science',
            ]),
            default => fake()->randomElement([
                'Bachelor of Science in Business Administration',
                'Bachelor of Arts in Psychology',
                'Bachelor of Science in Biology',
            ]),
        };
    }

    /**
     * Get scholarship-specific profile data
     */
    private function getScholarshipSpecificProfile(string $scholarshipType): array
    {
        return match ($scholarshipType) {
            'sports' => [
                'sport' => fake()->randomElement(['Basketball', 'Volleyball', 'Track and Field', 'Swimming', 'Football']),
                'position' => fake()->randomElement(['Forward', 'Guard', 'Center', 'Setter', 'Spiker']),
                'years_experience' => fake()->numberBetween(2, 10),
                'achievements' => [
                    fake()->sentence(6),
                    fake()->sentence(5),
                ],
            ],
            'need-based' => [
                'family_income' => fake()->numberBetween(8000, 45000),
                'family_size' => fake()->numberBetween(3, 8),
                'parent_occupation' => fake()->randomElement(['Fisherman', 'Farmer', 'Tricycle Driver', 'Market Vendor', 'Housewife']),
            ],
            'indigenous' => [
                'indigenous_group' => fake()->randomElement([
                    'Bantayan Island Native Community',
                    'Cebuano Indigenous Community',
                    'Traditional Fisher Community'
                ]),
                'cultural_involvement' => [
                    'Traditional dance performer',
                    'Cultural events organizer',
                ],
            ],
            default => [],
        };
    }

    /**
     * Get committee email based on scholarship
     */
    private function getCommitteeEmail(?string $scholarshipSlug): string
    {
        $prefix = $scholarshipSlug ? str_replace('-scholarship', '', $scholarshipSlug) : 'general';
        return $prefix . '.committee.' . fake()->randomNumber(2) . '@daanbantayan.gov.ph';
    }

    /**
     * Get department by scholarship type
     */
    private function getDepartmentByScholarship(?string $scholarshipType): string
    {
        return match ($scholarshipType) {
            'merit' => 'Municipal Education Office',
            'sports' => 'Municipal Sports Development Office',
            'need-based' => 'Municipal Social Welfare and Development Office',
            'indigenous' => 'Municipal Indigenous Peoples Affairs Office',
            default => 'General Affairs Office',
        };
    }

    /**
     * Get expertise by scholarship type
     */
    private function getExpertiseByScholarship(?string $scholarshipType): string
    {
        return match ($scholarshipType) {
            'merit' => fake()->randomElement([
                'Academic Excellence Evaluation',
                'Student Assessment and Development',
                'Educational Program Management'
            ]),
            'sports' => fake()->randomElement([
                'Athletic Performance Evaluation',
                'Sports Medicine and Athlete Welfare',
                'Sports Development and Coaching'
            ]),
            'need-based' => fake()->randomElement([
                'Socio-Economic Assessment',
                'Family Welfare and Financial Analysis',
                'Community Development'
            ]),
            'indigenous' => fake()->randomElement([
                'Indigenous Community Relations',
                'Cultural Preservation and Development',
                'Traditional Knowledge Systems'
            ]),
            default => 'General Administration',
        };
    }
}
