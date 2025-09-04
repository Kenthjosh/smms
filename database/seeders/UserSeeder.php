<?php

namespace Database\Seeders;

use App\Models\Scholarship;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('👥 Creating users...');

        // Get scholarships (they should exist from ScholarshipSeeder)
        $scholarships = Scholarship::all();

        if ($scholarships->isEmpty()) {
            $this->command->error('❌ No scholarships found! Please run ScholarshipSeeder first.');
            return;
        }

        // Create committee members for each scholarship
        $this->createCommitteeMembers($scholarships);

        // Create sample students (manual specific ones)
        $this->createSampleStudents($scholarships);

        // Create additional students using factory
        $this->createFactoryStudents($scholarships);

        $this->command->info('✅ Users created successfully!');
        $this->printUserSummary();
    }

    /**
     * Create committee members for each scholarship
     */
    private function createCommitteeMembers($scholarships): void
    {
        foreach ($scholarships as $scholarship) {
            // Create Committee Chair
            User::firstOrCreate([
                'email' => strtolower(str_replace(['-', ' '], ['', '.'], $scholarship->slug)) . '.committee.chair@daanbantayan.gov.ph'
            ], [
                'name' => $this->getCommitteeChairName($scholarship->slug),
                'password' => Hash::make('committee123'),
                'role' => 'committee',
                'scholarship_id' => $scholarship->id,
                'profile_data' => json_encode($this->getCommitteeProfile($scholarship, 'chair'))
            ]);

            // Create Committee Member (if scholarship needs more than 1 committee member)
            if (in_array($scholarship->slug, ['merit-scholarship', 'sports-scholarship', 'need-based-scholarship'])) {
                User::firstOrCreate([
                    'email' => strtolower(str_replace(['-', ' '], ['', '.'], $scholarship->slug)) . '.committee.member@daanbantayan.gov.ph'
                ], [
                    'name' => $this->getCommitteeMemberName($scholarship->slug),
                    'password' => Hash::make('committee123'),
                    'role' => 'committee',
                    'scholarship_id' => $scholarship->id,
                    'profile_data' => json_encode($this->getCommitteeProfile($scholarship, 'member'))
                ]);
            }
        }

        $committeeCount = User::where('role', 'committee')->count();
        $this->command->info("   👨‍💼 Created {$committeeCount} committee members");
    }

    /**
     * Create sample students with specific profiles
     */
    private function createSampleStudents($scholarships): void
    {
        $sampleStudents = $this->getSampleStudentData($scholarships);

        foreach ($sampleStudents as $studentData) {
            User::firstOrCreate([
                'email' => $studentData['email']
            ], $studentData);
        }

        $this->command->info("   🎓 Created " . count($sampleStudents) . " sample students");
    }

    /**
     * Create additional students using factory
     */
    private function createFactoryStudents($scholarships): void
    {
        $studentsPerScholarship = 50; // Create 50 students per scholarship

        foreach ($scholarships as $scholarship) {
            User::factory()
                ->count($studentsPerScholarship)
                ->student($scholarship->id)
                ->create();
        }

        $totalFactoryStudents = $scholarships->count() * $studentsPerScholarship;
        $this->command->info("   🏭 Created {$totalFactoryStudents} factory students ({$studentsPerScholarship} per scholarship)");
    }

    /**
     * Get sample student data
     */
    private function getSampleStudentData($scholarships): array
    {
        $merit = $scholarships->where('slug', 'merit-scholarship')->first();
        $sports = $scholarships->where('slug', 'sports-scholarship')->first();
        $needBased = $scholarships->where('slug', 'need-based-scholarship')->first();
        $indigenous = $scholarships->where('slug', 'indigenous-scholarship')->first();

        return [
            // Merit Scholarship Students
            [
                'name' => 'Anna Marie T. Gonzalez',
                'email' => 'anna.gonzalez@student.daanbantayan.edu.ph',
                'password' => Hash::make('student123'),
                'role' => 'student',
                'scholarship_id' => $merit->id,
                'profile_data' => json_encode([
                    'student_id' => 'MS-2024-001',
                    'school' => 'University of San Carlos',
                    'course' => 'Bachelor of Science in Computer Science',
                    'year_level' => '3rd Year',
                    'gpa' => 3.92,
                    'class_rank' => '2 of 150',
                    'contact_number' => '+63 999 501 1111',
                    'address' => 'Barangay Poblacion, Daanbantayan, Cebu',
                    'parent_guardian' => 'Mr. Antonio Gonzalez & Mrs. Maria Gonzalez',
                    'parent_contact' => '+63 999 501 2222',
                    'achievements' => [
                        'Dean\'s List (6 consecutive semesters)',
                        'Outstanding Student in Computer Science 2023',
                        'Programming Competition Champion - Regional Level'
                    ]
                ])
            ],
            [
                'name' => 'John Michael L. Torres',
                'email' => 'john.torres@student.daanbantayan.edu.ph',
                'password' => Hash::make('student123'),
                'role' => 'student',
                'scholarship_id' => $merit->id,
                'profile_data' => json_encode([
                    'student_id' => 'MS-2024-002',
                    'school' => 'Cebu Institute of Technology - University',
                    'course' => 'Bachelor of Science in Civil Engineering',
                    'year_level' => '2nd Year',
                    'gpa' => 3.85,
                    'class_rank' => '5 of 120',
                    'contact_number' => '+63 999 502 1111',
                    'address' => 'Sitio Lawis, Barangay Maya, Daanbantayan, Cebu',
                    'achievements' => [
                        'Consistent Honor Student',
                        'Math Olympiad Regional Qualifier',
                        'Student Council Academic Affairs Officer'
                    ]
                ])
            ],

            // Sports Scholarship Students
            [
                'name' => 'Miguel Angelo B. Reyes',
                'email' => 'miguel.reyes@student.daanbantayan.edu.ph',
                'password' => Hash::make('student123'),
                'role' => 'student',
                'scholarship_id' => $sports->id,
                'profile_data' => json_encode([
                    'student_id' => 'SS-2024-001',
                    'school' => 'University of the Philippines Cebu',
                    'course' => 'Bachelor of Science in Sports Science',
                    'year_level' => '4th Year',
                    'gpa' => 3.2,
                    'sport' => 'Basketball',
                    'position' => 'Point Guard',
                    'achievements' => [
                        'CESAFI Season MVP 2023',
                        'Regional Basketball Championship 2022-2023',
                        'University Varsity Team Captain'
                    ],
                    'athletic_records' => [
                        'Points per game average: 18.5',
                        'Assists per game: 8.2',
                        'Free throw percentage: 87%'
                    ]
                ])
            ],
            [
                'name' => 'Sofia Isabella C. Cruz',
                'email' => 'sofia.cruz@student.daanbantayan.edu.ph',
                'password' => Hash::make('student123'),
                'role' => 'student',
                'scholarship_id' => $sports->id,
                'profile_data' => json_encode([
                    'student_id' => 'SS-2024-002',
                    'school' => 'Cebu Normal University',
                    'course' => 'Bachelor of Science in Physical Education',
                    'year_level' => '3rd Year',
                    'gpa' => 3.0,
                    'sport' => 'Volleyball',
                    'position' => 'Setter',
                    'achievements' => [
                        'Provincial Volleyball Championship - Best Setter',
                        'Inter-School Sports Festival MVP',
                        'University Women\'s Volleyball Team Captain'
                    ]
                ])
            ],

            // Need-Based Scholarship Students
            [
                'name' => 'Maria Carmela D. Lopez',
                'email' => 'maria.lopez@student.daanbantayan.edu.ph',
                'password' => Hash::make('student123'),
                'role' => 'student',
                'scholarship_id' => $needBased->id,
                'profile_data' => json_encode([
                    'student_id' => 'NS-2024-001',
                    'school' => 'Cebu Doctors\' University',
                    'course' => 'Bachelor of Science in Nursing',
                    'year_level' => '2nd Year',
                    'gpa' => 3.1,
                    'family_background' => 'Father is a fisherman, mother is a housewife',
                    'siblings_count' => 4,
                    'family_monthly_income' => '₱18,000',
                    'financial_need' => 'Primary breadwinner for education expenses'
                ])
            ],
            [
                'name' => 'Jose Antonio E. Martinez',
                'email' => 'jose.martinez@student.daanbantayan.edu.ph',
                'password' => Hash::make('student123'),
                'role' => 'student',
                'scholarship_id' => $needBased->id,
                'profile_data' => json_encode([
                    'student_id' => 'NS-2024-002',
                    'school' => 'Cebu Technological University',
                    'course' => 'Bachelor of Science in Information Technology',
                    'year_level' => '1st Year',
                    'gpa' => 2.8,
                    'family_background' => 'Single mother, domestic helper',
                    'siblings_count' => 3,
                    'family_monthly_income' => '₱15,000',
                    'part_time_work' => 'Computer shop assistant on weekends'
                ])
            ],

            // Indigenous Scholarship Students
            [
                'name' => 'Lakambini Rose F. Fernandez',
                'email' => 'lakambini.fernandez@student.daanbantayan.edu.ph',
                'password' => Hash::make('student123'),
                'role' => 'student',
                'scholarship_id' => $indigenous->id,
                'profile_data' => json_encode([
                    'student_id' => 'IS-2024-001',
                    'school' => 'University of San Carlos',
                    'course' => 'Bachelor of Science in Anthropology',
                    'year_level' => '4th Year',
                    'gpa' => 3.3,
                    'indigenous_group' => 'Cebuano Indigenous Community',
                    'cultural_role' => 'Cultural preservation advocate and traditional dance performer',
                    'community_involvement' => [
                        'Cultural festival organizer',
                        'Indigenous language preservation advocate',
                        'Community youth mentor'
                    ]
                ])
            ],
            [
                'name' => 'Carlos Miguel G. Bayani',
                'email' => 'carlos.bayani@student.daanbantayan.edu.ph',
                'password' => Hash::make('student123'),
                'role' => 'student',
                'scholarship_id' => $indigenous->id,
                'profile_data' => json_encode([
                    'student_id' => 'IS-2024-002',
                    'school' => 'Cebu Normal University',
                    'course' => 'Bachelor of Science in Environmental Science',
                    'year_level' => '2nd Year',
                    'gpa' => 2.9,
                    'indigenous_group' => 'Local Tribal Community',
                    'cultural_background' => 'Traditional farming and environmental stewardship'
                ])
            ]
        ];
    }

    /**
     * Get committee chair name based on scholarship
     */
    private function getCommitteeChairName($scholarshipSlug): string
    {
        return match ($scholarshipSlug) {
            'merit-scholarship' => 'Dr. Maria Elena Santos',
            'sports-scholarship' => 'Coach Roberto Villareal',
            'need-based-scholarship' => 'Ms. Carmen Reyes-Dela Cruz',
            'indigenous-scholarship' => 'Prof. Eduardo Magbanua',
            default => 'Committee Chair'
        };
    }

    /**
     * Get committee member name based on scholarship
     */
    private function getCommitteeMemberName($scholarshipSlug): string
    {
        return match ($scholarshipSlug) {
            'merit-scholarship' => 'Prof. Antonio dela Rosa',
            'sports-scholarship' => 'Ms. Jennifer Tan-Abad',
            'need-based-scholarship' => 'Mr. Ricardo Jimenez',
            default => 'Committee Member'
        };
    }

    /**
     * Get committee profile data
     */
    private function getCommitteeProfile($scholarship, $role): array
    {
        $baseProfile = [
            'position' => ucwords($role) . ' - ' . $scholarship->name,
            'department' => $this->getDepartmentByScholarship($scholarship->slug),
            'contact_number' => '+63 999 ' . rand(100, 999) . ' ' . rand(1000, 9999),
            'employee_id' => strtoupper(substr($scholarship->slug, 0, 3)) . '-2024-' . sprintf('%03d', rand(1, 999)),
        ];

        $specificProfile = match ($scholarship->slug) {
            'merit-scholarship' => [
                'expertise' => 'Academic Excellence Evaluation',
                'education' => $role === 'chair' ? 'PhD in Education Administration' : 'Master in Educational Leadership',
            ],
            'sports-scholarship' => [
                'expertise' => 'Athletic Performance Assessment',
                'background' => $role === 'chair' ? 'Former Professional Athlete' : 'Sports Management Specialist',
            ],
            'need-based-scholarship' => [
                'expertise' => 'Socio-economic Assessment',
                'specialization' => 'Financial Need Evaluation and Social Work',
            ],
            'indigenous-scholarship' => [
                'expertise' => 'Cultural Preservation and Indigenous Affairs',
                'background' => 'Community Development and Cultural Studies',
            ],
            default => []
        };

        return array_merge($baseProfile, $specificProfile);
    }

    /**
     * Get department by scholarship type
     */
    private function getDepartmentByScholarship($scholarshipSlug): string
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
     * Print user creation summary
     */
    private function printUserSummary(): void
    {
        $this->command->info('');
        $this->command->info('📊 User Creation Summary:');

        foreach (Scholarship::all() as $scholarship) {
            $committee = User::where('role', 'committee')->where('scholarship_id', $scholarship->id)->count();
            $students = User::where('role', 'student')->where('scholarship_id', $scholarship->id)->count();

            $this->command->info("   {$scholarship->name}:");
            $this->command->info("     👨‍💼 Committee: {$committee}");
            $this->command->info("     🎓 Students: {$students}");
        }

        $totalUsers = User::count();
        $this->command->info("   📊 Total Users: {$totalUsers}");
    }
}
