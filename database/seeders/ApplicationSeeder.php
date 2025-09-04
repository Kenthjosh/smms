<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Application;
use App\Models\User;
use App\Models\Scholarship;
use Carbon\Carbon;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📄 Creating applications...');

        // Get all scholarships and students
        $scholarships = Scholarship::all();

        if ($scholarships->isEmpty()) {
            $this->command->error('❌ No scholarships found! Please run ScholarshipSeeder first.');
            return;
        }

        foreach ($scholarships as $scholarship) {
            $this->createApplicationsForScholarship($scholarship);
        }

        $this->command->info('✅ Applications created successfully!');
        $this->printSummary();
    }

    /**
     * Create applications for a specific scholarship
     */
    private function createApplicationsForScholarship(Scholarship $scholarship): void
    {
        $students = User::where('role', 'student')
            ->where('scholarship_id', $scholarship->id)
            ->get();

        $committeeMembers = User::where('role', 'committee')
            ->where('scholarship_id', $scholarship->id)
            ->get();

        if ($students->isEmpty()) {
            $this->command->warn("⚠️  No students found for {$scholarship->name}");
            return;
        }

        $this->command->info("   Creating applications for {$scholarship->name} ({$students->count()} students)");

        // Determine realistic distribution
        $distributions = $this->calculateDistribution($students->count());
        $studentIndex = 0;

        // Create approved applications (25%)
        for ($i = 0; $i < $distributions['approved']; $i++) {
            if ($studentIndex >= $students->count()) break;

            $student = $students[$studentIndex++];
            $reviewer = $committeeMembers->isNotEmpty() ? $committeeMembers->random() : null;

            $this->createApplication($scholarship, $student, 'approved', $reviewer);
        }

        // Create rejected applications (15%)
        for ($i = 0; $i < $distributions['rejected']; $i++) {
            if ($studentIndex >= $students->count()) break;

            $student = $students[$studentIndex++];
            $reviewer = $committeeMembers->isNotEmpty() ? $committeeMembers->random() : null;

            $this->createApplication($scholarship, $student, 'rejected', $reviewer);
        }

        // Create under review applications (20%)
        for ($i = 0; $i < $distributions['under_review']; $i++) {
            if ($studentIndex >= $students->count()) break;

            $student = $students[$studentIndex++];
            $this->createApplication($scholarship, $student, 'under_review');
        }

        // Create submitted applications (25%)
        for ($i = 0; $i < $distributions['submitted']; $i++) {
            if ($studentIndex >= $students->count()) break;

            $student = $students[$studentIndex++];
            $this->createApplication($scholarship, $student, 'submitted');
        }

        // Remaining students get draft applications (15%)
        while ($studentIndex < $students->count()) {
            $student = $students[$studentIndex++];
            $this->createApplication($scholarship, $student, 'draft');
        }

        $created = Application::where('scholarship_id', $scholarship->id)->count();
        $this->command->info("     ✅ Created {$created} applications");
    }

    /**
     * Create a single application
     */
    private function createApplication(
        Scholarship $scholarship,
        User $student,
        string $status,
        ?User $reviewer = null
    ): void {
        // Skip if application already exists
        if (Application::where('scholarship_id', $scholarship->id)
            ->where('user_id', $student->id)
            ->exists()
        ) {
            return;
        }

        $applicationData = $this->generateApplicationData($scholarship, $student);
        $dates = $this->generateDates($status);

        Application::create([
            'scholarship_id' => $scholarship->id,
            'user_id' => $student->id,
            'application_data' => json_encode($applicationData),
            'status' => $status,
            'committee_notes' => $this->generateCommitteeNotes($status),
            'submitted_at' => $dates['submitted_at'],
            'reviewed_at' => $dates['reviewed_at'],
            'reviewed_by' => $reviewer?->id,
            'created_at' => $dates['created_at'],
            'updated_at' => $dates['updated_at'],
        ]);
    }

    /**
     * Calculate realistic status distribution
     */
    private function calculateDistribution(int $totalStudents): array
    {
        if ($totalStudents <= 1) {
            return [
                'approved' => 0,
                'rejected' => 0,
                'under_review' => 0,
                'submitted' => 0,
                'draft' => $totalStudents
            ];
        }

        return [
            'approved' => max(1, intval($totalStudents * 0.25)),     // 25%
            'rejected' => max(1, intval($totalStudents * 0.15)),     // 15%
            'under_review' => max(1, intval($totalStudents * 0.20)), // 20%
            'submitted' => max(1, intval($totalStudents * 0.25)),    // 25%
            'draft' => max(0, intval($totalStudents * 0.15))         // 15%
        ];
    }

    /**
     * Generate application data based on scholarship type
     */
    private function generateApplicationData(Scholarship $scholarship, User $student): array
    {
        $baseData = [
            'personal_information' => [
                'full_name' => $student->name,
                'date_of_birth' => fake()->date('Y-m-d', '2005-01-01'),
                'gender' => fake()->randomElement(['Male', 'Female']),
                'civil_status' => 'Single',
                'nationality' => 'Filipino',
                'contact_number' => '+63 9' . fake()->numberBetween(100000000, 999999999),
                'email_address' => $student->email,
                'permanent_address' => fake()->address() . ', Daanbantayan, Cebu'
            ],
            'academic_information' => [
                'current_gpa' => fake()->numberBetween(250, 400) / 100,
                'school' => fake()->randomElement([
                    'University of San Carlos',
                    'Cebu Institute of Technology',
                    'University of the Philippines Cebu',
                    'Cebu Normal University'
                ]),
                'course' => $this->getRandomCourse($scholarship->slug),
                'year_level' => fake()->randomElement(['1st Year', '2nd Year', '3rd Year', '4th Year']),
                'expected_graduation' => fake()->date('Y-m-d', '+3 years')
            ]
        ];

        // Add scholarship-specific data
        $specificData = match ($scholarship->slug) {
            'merit-scholarship' => [
                'achievements' => [
                    'academic_awards' => ['Dean\'s List', 'Academic Excellence Award'],
                    'extracurricular_activities' => ['Student Government', 'Honor Society'],
                    'leadership_roles' => ['Class President', 'Organization Officer']
                ],
                'essay_responses' => [
                    'career_goals' => 'I aim to excel in my chosen field and contribute to community development.',
                    'why_deserving' => 'My consistent academic performance demonstrates my commitment to excellence.'
                ]
            ],
            'sports-scholarship' => [
                'sports_information' => [
                    'primary_sport' => fake()->randomElement(['Basketball', 'Volleyball', 'Swimming', 'Track and Field']),
                    'years_experience' => fake()->numberBetween(3, 10) . ' years',
                    'position_played' => fake()->randomElement(['Forward', 'Guard', 'Center', 'Midfielder']),
                    'team_affiliation' => 'School Varsity Team'
                ],
                'achievements' => [
                    'competitions' => ['Regional Championships', 'Inter-school Tournament'],
                    'awards' => ['Most Valuable Player', 'Best Athlete Award'],
                    'records_held' => ['School Record in 100m Sprint']
                ],
                'training_schedule' => [
                    'training_hours_per_week' => fake()->numberBetween(15, 25),
                    'training_days' => ['Monday', 'Tuesday', 'Thursday', 'Friday'],
                    'fitness_level' => 'Excellent'
                ]
            ],
            'need-based-scholarship' => [
                'family_information' => [
                    'father_name' => fake()->name('male'),
                    'father_occupation' => fake()->randomElement(['Farmer', 'Fisherman', 'Driver', 'Construction Worker']),
                    'father_monthly_income' => fake()->randomElement(['₱8,000', '₱12,000', '₱15,000']),
                    'mother_name' => fake()->name('female'),
                    'mother_occupation' => fake()->randomElement(['Housewife', 'Vendor', 'Domestic Helper']),
                    'mother_monthly_income' => fake()->randomElement(['₱5,000', '₱8,000', '₱10,000']),
                    'number_of_siblings' => fake()->numberBetween(2, 5),
                    'siblings_in_school' => fake()->numberBetween(1, 3),
                    'family_monthly_income' => fake()->randomElement(['₱15,000', '₱20,000', '₱25,000'])
                ],
                'financial_information' => [
                    'monthly_expenses' => [
                        'tuition' => '₱8,000',
                        'books_supplies' => '₱1,500',
                        'transportation' => '₱2,000',
                        'food' => '₱4,000'
                    ],
                    'source_of_funding' => 'Family Income and Part-time Work',
                    'financial_difficulties' => ['Insufficient family income', 'Multiple siblings in school']
                ],
                'essay_responses' => [
                    'financial_need_explanation' => 'Our family struggles financially with limited income and multiple children to support.',
                    'how_scholarship_helps' => 'This scholarship would relieve financial burden and allow me to focus on studies.'
                ]
            ],
            'indigenous-scholarship' => [
                'cultural_information' => [
                    'indigenous_group' => fake()->randomElement(['Cebuano Indigenous', 'Local Tribal Community']),
                    'tribal_affiliation' => 'Recognized Tribal Member',
                    'cultural_practices' => ['Traditional farming', 'Cultural preservation', 'Indigenous language'],
                    'language_spoken' => 'Cebuano and indigenous dialect',
                    'community_role' => 'Youth Cultural Leader'
                ],
                'family_background' => [
                    'parents_indigenous_status' => 'Both parents are indigenous community members',
                    'family_occupation' => 'Subsistence farming and traditional crafts',
                    'community_location' => 'Indigenous ancestral domain in Daanbantayan'
                ],
                'goals_and_aspirations' => [
                    'career_goals' => 'Become an advocate for indigenous rights and cultural preservation',
                    'community_commitment' => 'Return to community and help preserve our cultural heritage'
                ]
            ],
            default => []
        };

        return array_merge($baseData, $specificData);
    }

    /**
     * Get random course based on scholarship type
     */
    private function getRandomCourse(string $scholarshipSlug): string
    {
        return match ($scholarshipSlug) {
            'merit-scholarship' => fake()->randomElement([
                'BS Computer Science',
                'BS Engineering',
                'BS Mathematics',
                'BS Physics',
                'BS Chemistry'
            ]),
            'sports-scholarship' => fake()->randomElement([
                'BS Physical Education',
                'BS Sports Science',
                'BS Kinesiology',
                'BS Recreation'
            ]),
            'need-based-scholarship' => fake()->randomElement([
                'BS Education',
                'BS Social Work',
                'BS Nursing',
                'BS Business Administration'
            ]),
            'indigenous-scholarship' => fake()->randomElement([
                'BS Anthropology',
                'BS Environmental Science',
                'BS Agriculture',
                'BS Social Work'
            ]),
            default => 'General Studies'
        };
    }

    /**
     * Generate dates based on status
     */
    private function generateDates(string $status): array
    {
        $now = Carbon::now();

        return match ($status) {
            'approved' => [
                'created_at' => $now->copy()->subDays(rand(30, 60)),
                'submitted_at' => $now->copy()->subDays(rand(25, 55)),
                'reviewed_at' => $now->copy()->subDays(rand(5, 20)),
                'updated_at' => $now->copy()->subDays(rand(5, 20))
            ],
            'rejected' => [
                'created_at' => $now->copy()->subDays(rand(30, 60)),
                'submitted_at' => $now->copy()->subDays(rand(25, 55)),
                'reviewed_at' => $now->copy()->subDays(rand(5, 20)),
                'updated_at' => $now->copy()->subDays(rand(5, 20))
            ],
            'under_review' => [
                'created_at' => $now->copy()->subDays(rand(10, 30)),
                'submitted_at' => $now->copy()->subDays(rand(7, 25)),
                'reviewed_at' => null,
                'updated_at' => $now->copy()->subDays(rand(1, 7))
            ],
            'submitted' => [
                'created_at' => $now->copy()->subDays(rand(5, 15)),
                'submitted_at' => $now->copy()->subDays(rand(1, 10)),
                'reviewed_at' => null,
                'updated_at' => $now->copy()->subDays(rand(1, 10))
            ],
            'draft' => [
                'created_at' => $now->copy()->subDays(rand(1, 30)),
                'submitted_at' => null,
                'reviewed_at' => null,
                'updated_at' => $now->copy()->subDays(rand(1, 7))
            ],
            default => [
                'created_at' => $now->copy()->subDays(rand(1, 30)),
                'submitted_at' => null,
                'reviewed_at' => null,
                'updated_at' => $now->copy()->subDays(rand(1, 7))
            ]
        };
    }

    /**
     * Generate committee notes based on status
     */
    private function generateCommitteeNotes(?string $status): ?string
    {
        return match ($status) {
            'approved' => fake()->randomElement([
                'Application approved. Excellent academic performance and strong qualifications.',
                'Committee unanimously approved this outstanding application.',
                'Meets all requirements with exceptional credentials.',
                'Approved for full scholarship benefits. Congratulations!'
            ]),
            'rejected' => fake()->randomElement([
                'Application does not meet minimum GPA requirements.',
                'Incomplete documentation submitted.',
                'Does not meet financial need criteria.',
                'Application submitted after deadline.'
            ]),
            'under_review' => fake()->randomElement([
                'Application is complete and under committee review.',
                'Reviewing academic credentials and supporting documents.',
                'Awaiting additional verification from school.',
                'Committee meeting scheduled for final decision.'
            ]),
            default => null
        };
    }

    /**
     * Print summary of created applications
     */
    private function printSummary(): void
    {
        $this->command->info('');
        $this->command->info('📊 Application Summary:');

        $scholarships = Scholarship::withCount(['applications'])->get();

        foreach ($scholarships as $scholarship) {
            $statusCounts = Application::where('scholarship_id', $scholarship->id)
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $this->command->info("   {$scholarship->name}: {$scholarship->applications_count} total");

            foreach (['draft', 'submitted', 'under_review', 'approved', 'rejected'] as $status) {
                $count = $statusCounts[$status] ?? 0;
                if ($count > 0) {
                    $emoji = match ($status) {
                        'draft' => '✏️',
                        'submitted' => '📤',
                        'under_review' => '🔍',
                        'approved' => '✅',
                        'rejected' => '❌',
                        default => '📄'
                    };
                    $this->command->info("     {$emoji} {$status}: {$count}");
                }
            }
        }

        $totalApplications = Application::count();
        $this->command->info("🎉 Total applications created: {$totalApplications}");

        // Show status distribution across all scholarships
        $this->command->info('');
        $this->command->info('📈 Overall Status Distribution:');
        $overallStatus = Application::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        foreach (['draft', 'submitted', 'under_review', 'approved', 'rejected'] as $status) {
            $count = $overallStatus[$status] ?? 0;
            $percentage = $totalApplications > 0 ? round(($count / $totalApplications) * 100, 1) : 0;
            if ($count > 0) {
                $emoji = match ($status) {
                    'draft' => '✏️',
                    'submitted' => '📤',
                    'under_review' => '🔍',
                    'approved' => '✅',
                    'rejected' => '❌',
                    default => '📄'
                };
                $this->command->info("   {$emoji} {$status}: {$count} ({$percentage}%)");
            }
        }
    }
}
