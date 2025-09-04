<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Application;
use App\Models\User;
use App\Models\Scholarship;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating scholarship applications...');

        // Get all scholarships
        $scholarships = Scholarship::all();

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
            $this->command->warn("No students found for {$scholarship->name}");
            return;
        }

        $this->command->info("Creating applications for {$scholarship->name} ({$students->count()} students)");

        // Determine application distribution
        $totalStudents = $students->count();
        $distributions = $this->calculateApplicationDistribution($totalStudents);

        $studentIndex = 0;

        // Create approved applications (with reviewers)
        for ($i = 0; $i < $distributions['approved']; $i++) {
            if ($studentIndex >= $totalStudents) break;

            $student = $students[$studentIndex++];
            $reviewer = $committeeMembers->isNotEmpty() ? $committeeMembers->random() : null;

            $this->createApplicationByType($scholarship, $student, 'approved', $reviewer);
        }

        // Create rejected applications (with reviewers)
        for ($i = 0; $i < $distributions['rejected']; $i++) {
            if ($studentIndex >= $totalStudents) break;

            $student = $students[$studentIndex++];
            $reviewer = $committeeMembers->isNotEmpty() ? $committeeMembers->random() : null;

            $this->createApplicationByType($scholarship, $student, 'rejected', $reviewer);
        }

        // Create under review applications
        for ($i = 0; $i < $distributions['under_review']; $i++) {
            if ($studentIndex >= $totalStudents) break;

            $student = $students[$studentIndex++];
            $this->createApplicationByType($scholarship, $student, 'under_review');
        }

        // Create submitted applications
        for ($i = 0; $i < $distributions['submitted']; $i++) {
            if ($studentIndex >= $totalStudents) break;

            $student = $students[$studentIndex++];
            $this->createApplicationByType($scholarship, $student, 'submitted');
        }

        // Create draft applications for remaining students
        while ($studentIndex < $totalStudents) {
            $student = $students[$studentIndex++];
            $this->createApplicationByType($scholarship, $student, 'draft');
        }
    }

    /**
     * Create application by type using the factory
     */
    private function createApplicationByType(
        Scholarship $scholarship,
        User $student,
        string $type,
        ?User $reviewer = null
    ): void {
        $factory = Application::factory()
            ->forScholarshipAndUser($scholarship, $student);

        // Apply scholarship-specific data generation
        $factory = match ($scholarship->slug) {
            'merit-scholarship' => $factory->merit(),
            'sports-scholarship' => $factory->sports(),
            'need-based-scholarship' => $factory->needBased(),
            'indigenous-scholarship' => $factory->indigenous(),
            default => $factory
        };

        // Apply status-specific configurations
        $factory = match ($type) {
            'approved' => $factory->approved(),
            'rejected' => $factory->rejected(),
            'under_review' => $factory->underReview(),
            'submitted' => $factory->submitted(),
            'draft' => $factory->draft(),
            default => $factory
        };

        // Add reviewer if provided
        if ($reviewer) {
            $factory = $factory->withReviewer($reviewer);
        }

        // Create the application
        $factory->create();
    }

    /**
     * Generate incomplete application data for abandoned drafts
     */
    private function generateIncompleteApplicationData(Scholarship $scholarship, User $user): string
    {
        $baseData = [
            'personal_information' => [
                'full_name' => $user->name,
                'date_of_birth' => null, // Often left incomplete
                'gender' => fake()->randomElement(['Male', 'Female', null]),
                'civil_status' => fake()->randomElement(['Single', null]),
            ],
            'academic_information' => [
                'current_gpa' => null, // Often the part students get stuck on
                'school' => fake()->randomElement(['', 'University Name', null]),
                'course' => null,
                'year_level' => fake()->randomElement(['1st Year', null])
            ],
            '_incomplete_sections' => fake()->randomElements([
                'academic_information',
                'family_information',
                'essay_responses',
                'achievements',
                'sports_information'
            ], fake()->numberBetween(1, 3)),
            '_last_section_worked_on' => fake()->randomElement([
                'personal_information',
                'academic_information',
                'family_information'
            ]),
            '_completion_percentage' => fake()->numberBetween(20, 65)
        ];

        // Add some scholarship-specific incomplete fields
        if ($scholarship->slug === 'sports-scholarship') {
            $baseData['sports_information'] = [
                'primary_sport' => fake()->randomElement(['Basketball', null]),
                'years_experience' => null,
                'team_affiliation' => null
            ];
        } elseif ($scholarship->slug === 'need-based-scholarship') {
            $baseData['family_information'] = [
                'father_name' => fake()->randomElement([fake()->name('male'), null]),
                'mother_name' => null,
                'family_monthly_income' => null
            ];
        } elseif ($scholarship->slug === 'indigenous-scholarship') {
            $baseData['cultural_information'] = [
                'indigenous_group' => null,
                'cultural_practices' => []
            ];
        }

        return json_encode($baseData);
    }

    /**
     * Calculate application status distribution based on total students
     */
    private function calculateApplicationDistribution(int $totalStudents): array
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

        // Calculate percentages for realistic distribution
        $approved = max(1, intval($totalStudents * 0.25));      // 25% approved
        $rejected = max(1, intval($totalStudents * 0.15));      // 15% rejected
        $underReview = max(1, intval($totalStudents * 0.20));   // 20% under review
        $submitted = max(1, intval($totalStudents * 0.25));     // 25% submitted

        // Remaining are drafts
        $assigned = $approved + $rejected + $underReview + $submitted;
        $draft = max(0, $totalStudents - $assigned);

        return [
            'approved' => $approved,
            'rejected' => $rejected,
            'under_review' => $underReview,
            'submitted' => $submitted,
            'draft' => $draft
        ];
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

            // Separate current vs historical
            $currentCounts = Application::where('scholarship_id', $scholarship->id)
                ->where('created_at', '>=', now()->subDays(15))
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $historicalCounts = Application::where('scholarship_id', $scholarship->id)
                ->where('created_at', '<', now()->subDays(15))
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $this->command->info("  {$scholarship->name}: {$scholarship->applications_count} total");

            // Show current applications
            $this->command->info("    📄 Current Applications:");
            foreach (['draft', 'submitted', 'under_review', 'approved', 'rejected'] as $status) {
                $count = $currentCounts[$status] ?? 0;
                if ($count > 0) {
                    $emoji = match ($status) {
                        'draft' => '✏️',
                        'submitted' => '📤',
                        'under_review' => '🔍',
                        'approved' => '✅',
                        'rejected' => '❌',
                        default => '📄'
                    };
                    $this->command->info("      {$emoji} {$status}: {$count}");
                }
            }

            // Show historical applications if any
            $historicalTotal = array_sum($historicalCounts);
            if ($historicalTotal > 0) {
                $this->command->info("    📜 Historical Applications: {$historicalTotal}");
                foreach (['rejected', 'draft'] as $status) {
                    $count = $historicalCounts[$status] ?? 0;
                    if ($count > 0) {
                        $emoji = $status === 'rejected' ? '🗂️' : '📝';
                        $label = $status === 'rejected' ? 'previous rejections' : 'abandoned drafts';
                        $this->command->info("      {$emoji} {$label}: {$count}");
                    }
                }
            }

            $this->command->info('');
        }

        $totalApplications = Application::count();
        $currentApplications = Application::where('created_at', '>=', now()->subDays(15))->count();
        $historicalApplications = $totalApplications - $currentApplications;

        $this->command->info("🎉 Total applications created: {$totalApplications}");
        $this->command->info("   📄 Current: {$currentApplications} | 📜 Historical: {$historicalApplications}");
    }
}
