<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
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
        // Get scholarships and students
        $meritScholarship = Scholarship::where('slug', 'merit-scholarship')->first();
        $sportsScholarship = Scholarship::where('slug', 'sports-scholarship')->first();
        $needBasedScholarship = Scholarship::where('slug', 'need-based-scholarship')->first();
        $indigenousScholarship = Scholarship::where('slug', 'indigenous-scholarship')->first();

        // Get students by scholarship
        $meritStudents = User::where('role', 'student')->where('scholarship_id', $meritScholarship->id)->get();
        $sportsStudents = User::where('role', 'student')->where('scholarship_id', $sportsScholarship->id)->get();
        $needBasedStudents = User::where('role', 'student')->where('scholarship_id', $needBasedScholarship->id)->get();
        $indigenousStudents = User::where('role', 'student')->where('scholarship_id', $indigenousScholarship->id)->get();

        // Get committee members for review assignments
        $meritCommittee = User::where('role', 'committee')->where('scholarship_id', $meritScholarship->id)->get();
        $sportsCommittee = User::where('role', 'committee')->where('scholarship_id', $sportsScholarship->id)->get();
        $needBasedCommittee = User::where('role', 'committee')->where('scholarship_id', $needBasedScholarship->id)->get();
        $indigenousCommittee = User::where('role', 'committee')->where('scholarship_id', $indigenousScholarship->id)->get();

        // === MERIT SCHOLARSHIP APPLICATIONS ===

        // Anna Gonzalez - Approved Application
        if ($meritStudents->count() > 0) {
            Application::firstOrCreate([
                'scholarship_id' => $meritScholarship->id,
                'user_id' => $meritStudents->first()->id, // Anna Gonzalez
            ], [
                'scholarship_id' => $meritScholarship->id,
                'user_id' => $meritStudents->first()->id, // Anna Gonzalez
                'application_data' => json_encode([
                    'personal_information' => [
                        'full_name' => 'Anna Marie T. Gonzalez',
                        'date_of_birth' => '2002-03-15',
                        'place_of_birth' => 'Daanbantayan, Cebu',
                        'gender' => 'Female',
                        'civil_status' => 'Single',
                        'nationality' => 'Filipino'
                    ],
                    'academic_information' => [
                        'current_gpa' => 3.92,
                        'class_rank' => '2 of 150',
                        'school' => 'University of San Carlos',
                        'course' => 'Bachelor of Science in Computer Science',
                        'year_level' => '3rd Year',
                        'expected_graduation' => '2025-04-30',
                        'previous_schools' => [
                            'Elementary: Daanbantayan Elementary School',
                            'High School: Daanbantayan National High School'
                        ]
                    ],
                    'achievements' => [
                        'Dean\'s List for 6 consecutive semesters',
                        'Outstanding Student in Computer Science 2023',
                        'Programming Competition Champion - Regional Level',
                        'President - Computer Science Society',
                        'Volunteer Tutor - Mathematics and Programming'
                    ],
                    'essays' => [
                        'academic_goals' => 'My goal is to become a software engineer specializing in educational technology. I want to develop applications that can help Filipino students access quality education, especially in rural areas like my hometown of Daanbantayan.',
                        'community_impact' => 'I have volunteered as a programming tutor for high school students in my barangay, teaching them basic computer skills and programming concepts. I plan to establish a computer literacy program for out-of-school youth.',
                        'scholarship_importance' => 'This scholarship will allow me to focus on my studies without the financial burden on my family. It will enable me to participate in internships and coding bootcamps that will enhance my skills.'
                    ],
                    'family_information' => [
                        'father_name' => 'Antonio Gonzalez',
                        'father_occupation' => 'Tricycle Driver',
                        'mother_name' => 'Maria Gonzalez',
                        'mother_occupation' => 'Housewife/Small Store Owner',
                        'siblings' => 2,
                        'family_monthly_income' => 25000
                    ]
                ]),
                'status' => 'approved',
                'committee_notes' => 'Exceptional academic performance with strong community involvement. Highly recommended for approval.',
                'submitted_at' => Carbon::now()->subDays(45),
                'reviewed_at' => Carbon::now()->subDays(30),
                'reviewed_by' => $meritCommittee->first()->id,
                'created_at' => Carbon::now()->subDays(50),
                'updated_at' => Carbon::now()->subDays(30)
            ]);
        }

        // John Torres - Under Review
        if ($meritStudents->count() > 1) {
            Application::firstOrCreate([
                'scholarship_id' => $meritScholarship->id,
                'user_id' => $meritStudents->get(1)->id, // John Torres
            ], [
                'scholarship_id' => $meritScholarship->id,
                'user_id' => $meritStudents->get(1)->id, // John Torres
                'application_data' => json_encode([
                    'personal_information' => [
                        'full_name' => 'John Michael L. Torres',
                        'date_of_birth' => '2003-07-22',
                        'place_of_birth' => 'Maya, Daanbantayan, Cebu',
                        'gender' => 'Male',
                        'civil_status' => 'Single',
                        'nationality' => 'Filipino'
                    ],
                    'academic_information' => [
                        'current_gpa' => 3.85,
                        'class_rank' => '5 of 120',
                        'school' => 'Cebu Institute of Technology - University',
                        'course' => 'Bachelor of Science in Civil Engineering',
                        'year_level' => '2nd Year',
                        'expected_graduation' => '2027-04-30'
                    ],
                    'achievements' => [
                        'Consistent Honor Student',
                        'Math Olympiad Regional Qualifier',
                        'Student Council Academic Affairs Officer',
                        'Engineering Society Vice President',
                        'Volunteer - Habitat for Humanity'
                    ],
                    'essays' => [
                        'academic_goals' => 'I aspire to become a structural engineer focusing on disaster-resilient infrastructure. Living in a typhoon-prone area, I understand the importance of building structures that can withstand natural disasters.',
                        'community_impact' => 'I have participated in community infrastructure projects, helping design small bridges and drainage systems in my barangay.',
                        'scholarship_importance' => 'Engineering education is expensive, and this scholarship will help me acquire the necessary skills and knowledge to serve my community better.'
                    ]
                ]),
                'status' => 'under_review',
                'committee_notes' => 'Strong academic record and relevant community involvement. Pending final documentation review.',
                'submitted_at' => Carbon::now()->subDays(20),
                'reviewed_at' => null,
                'reviewed_by' => null,
                'created_at' => Carbon::now()->subDays(25),
                'updated_at' => Carbon::now()->subDays(20)
            ]);
        }

        // === SPORTS SCHOLARSHIP APPLICATIONS ===

        // Miguel Reyes - Approved Application
        if ($sportsStudents->count() > 0) {
            Application::firstOrCreate([
                'scholarship_id' => $sportsScholarship->id,
                'user_id' => $sportsStudents->first()->id, // Miguel Reyes
            ], [
                'scholarship_id' => $sportsScholarship->id,
                'user_id' => $sportsStudents->first()->id, // Miguel Reyes
                'application_data' => json_encode([
                    'personal_information' => [
                        'full_name' => 'Miguel Angelo B. Reyes',
                        'date_of_birth' => '2001-11-08',
                        'place_of_birth' => 'Malingin, Daanbantayan, Cebu',
                        'gender' => 'Male',
                        'civil_status' => 'Single',
                        'nationality' => 'Filipino'
                    ],
                    'academic_information' => [
                        'current_gpa' => 3.2,
                        'school' => 'University of the Philippines Cebu',
                        'course' => 'Bachelor of Science in Sports Science',
                        'year_level' => '4th Year',
                        'expected_graduation' => '2025-04-30'
                    ],
                    'athletic_information' => [
                        'primary_sport' => 'Basketball',
                        'position' => 'Point Guard',
                        'years_of_experience' => 8,
                        'current_team' => 'UP Cebu Fighting Maroons',
                        'jersey_number' => 7
                    ],
                    'achievements' => [
                        'CESAFI Season MVP 2023',
                        'Regional Basketball Championship Winner 2022-2023',
                        'University Varsity Team Captain',
                        'Best Point Guard - Inter-collegiate Tournament 2023',
                        'Academic-Athletic Scholar for 3 years'
                    ],
                    'athletic_records' => [
                        'points_per_game' => 18.5,
                        'assists_per_game' => 8.2,
                        'rebounds_per_game' => 4.3,
                        'free_throw_percentage' => 87,
                        'three_point_percentage' => 42
                    ],
                    'career_goals' => 'I aim to play professional basketball while completing my degree, then transition to coaching and sports development, particularly in underserved communities.',
                    'community_involvement' => 'I conduct free basketball clinics for children in my barangay every summer, teaching not just basketball skills but also values and discipline.'
                ]),
                'status' => 'approved',
                'committee_notes' => 'Outstanding athletic performance combined with good academic standing and community involvement. Excellent role model for sports scholarship program.',
                'submitted_at' => Carbon::now()->subDays(35),
                'reviewed_at' => Carbon::now()->subDays(25),
                'reviewed_by' => $sportsCommittee->first()->id,
                'created_at' => Carbon::now()->subDays(40),
                'updated_at' => Carbon::now()->subDays(25)
            ]);
        }

        // Sofia Cruz - Submitted
        if ($sportsStudents->count() > 1) {
            Application::firstOrCreate([
                'scholarship_id' => $sportsScholarship->id,
                'user_id' => $sportsStudents->get(1)->id, // Sofia Cruz
            ], [
                'scholarship_id' => $sportsScholarship->id,
                'user_id' => $sportsStudents->get(1)->id, // Sofia Cruz
                'application_data' => json_encode([
                    'personal_information' => [
                        'full_name' => 'Sofia Isabella C. Cruz',
                        'date_of_birth' => '2004-02-14',
                        'place_of_birth' => 'Tapilon, Daanbantayan, Cebu',
                        'gender' => 'Female',
                        'civil_status' => 'Single',
                        'nationality' => 'Filipino'
                    ],
                    'academic_information' => [
                        'current_gpa' => 3.5,
                        'school' => 'Cebu Normal University',
                        'course' => 'Bachelor of Physical Education',
                        'year_level' => '1st Year',
                        'expected_graduation' => '2028-04-30'
                    ],
                    'athletic_information' => [
                        'primary_sport' => 'Volleyball',
                        'position' => 'Setter',
                        'years_of_experience' => 6,
                        'current_team' => 'CNU Lady Wildcats',
                        'jersey_number' => 9
                    ],
                    'achievements' => [
                        'Provincial Volleyball Championship Best Setter 2023',
                        'Inter-school Competition MVP',
                        'Regional Team Representative',
                        'High School Team Captain for 2 years',
                        'Academic Excellence Award - PE Department'
                    ],
                    'athletic_records' => [
                        'setting_accuracy' => 92,
                        'service_aces_per_set' => 2.3,
                        'team_win_percentage' => 78,
                        'assists_per_set' => 35,
                        'blocks_per_game' => 3.2
                    ]
                ]),
                'status' => 'submitted',
                'submitted_at' => Carbon::now()->subDays(10),
                'created_at' => Carbon::now()->subDays(15),
                'updated_at' => Carbon::now()->subDays(10)
            ]);
        }

        // === NEED-BASED SCHOLARSHIP APPLICATIONS ===

        // Maria Lopez - Approved
        if ($needBasedStudents->count() > 0) {
            Application::firstOrCreate([
                'scholarship_id' => $needBasedScholarship->id,
                'user_id' => $needBasedStudents->first()->id, // Maria Lopez
            ], [
                'scholarship_id' => $needBasedScholarship->id,
                'user_id' => $needBasedStudents->first()->id, // Maria Lopez
                'application_data' => json_encode([
                    'personal_information' => [
                        'full_name' => 'Maria Theresa S. Lopez',
                        'date_of_birth' => '2003-01-20',
                        'place_of_birth' => 'Carnaza, Daanbantayan, Cebu',
                        'gender' => 'Female',
                        'civil_status' => 'Single',
                        'nationality' => 'Filipino'
                    ],
                    'academic_information' => [
                        'current_gpa' => 3.1,
                        'school' => 'Cebu Doctors\' University',
                        'course' => 'Bachelor of Science in Nursing',
                        'year_level' => '2nd Year',
                        'expected_graduation' => '2027-04-30'
                    ],
                    'family_information' => [
                        'father_name' => 'Pedro Lopez',
                        'father_age' => 48,
                        'father_occupation' => 'Fisherman',
                        'father_monthly_income' => 12000,
                        'mother_name' => 'Rosa Lopez',
                        'mother_age' => 45,
                        'mother_occupation' => 'Housewife',
                        'mother_monthly_income' => 0,
                        'total_family_income' => 12000,
                        'family_size' => 7,
                        'siblings' => [
                            ['name' => 'Pedro Jr.', 'age' => 20, 'status' => 'Working (Fisherman)'],
                            ['name' => 'Carmen', 'age' => 16, 'status' => 'Grade 11 Student'],
                            ['name' => 'Jose', 'age' => 12, 'status' => 'Grade 6 Student'],
                            ['name' => 'Ana', 'age' => 8, 'status' => 'Grade 2 Student']
                        ]
                    ],
                    'financial_situation' => [
                        'monthly_expenses' => [
                            'food' => 8000,
                            'utilities' => 1500,
                            'education' => 2000,
                            'transportation' => 1000,
                            'medical' => 500,
                            'others' => 1000
                        ],
                        'assets' => [
                            'house' => 'Small wooden house (family-owned)',
                            'land' => 'Small residential lot',
                            'boat' => 'Small fishing boat (shared with relatives)',
                            'appliances' => 'Basic appliances only'
                        ],
                        'debts' => [
                            'education_loan' => 15000,
                            'medical_bills' => 5000
                        ]
                    ],
                    'personal_statement' => 'Coming from a family of fishermen, I have witnessed the struggles of my community in accessing proper healthcare. This motivates me to pursue nursing so I can serve my community and help improve healthcare services in remote areas like Carnaza Island.',
                    'goals' => 'After graduation, I plan to work in community health programs and eventually establish a clinic in our island to provide accessible healthcare to fellow islanders.'
                ]),
                'status' => 'approved',
                'committee_notes' => 'Clear financial need demonstrated with strong motivation to serve the community. Family income well below threshold. Approved for full scholarship support.',
                'submitted_at' => Carbon::now()->subDays(55),
                'reviewed_at' => Carbon::now()->subDays(40),
                'reviewed_by' => $needBasedCommittee->first()->id,
                'created_at' => Carbon::now()->subDays(60),
                'updated_at' => Carbon::now()->subDays(40)
            ]);
        }

        // Jose Martinez - Under Review
        if ($needBasedStudents->count() > 1) {
            Application::firstOrCreate([
                'scholarship_id' => $needBasedScholarship->id,
                'user_id' => $needBasedStudents->get(1)->id, // Jose Martinez
            ], [
                'scholarship_id' => $needBasedScholarship->id,
                'user_id' => $needBasedStudents->get(1)->id, // Jose Martinez
                'application_data' => json_encode([
                    'personal_information' => [
                        'full_name' => 'Jose Gabriel P. Martinez',
                        'date_of_birth' => '2002-09-12',
                        'place_of_birth' => 'Logon, Daanbantayan, Cebu',
                        'gender' => 'Male',
                        'civil_status' => 'Single',
                        'nationality' => 'Filipino'
                    ],
                    'academic_information' => [
                        'current_gpa' => 2.8,
                        'school' => 'Cebu Technological University',
                        'course' => 'Bachelor of Science in Information Technology',
                        'year_level' => '3rd Year',
                        'expected_graduation' => '2026-04-30'
                    ],
                    'family_information' => [
                        'mother_name' => 'Ana Martinez',
                        'mother_age' => 42,
                        'mother_occupation' => 'Market Vendor',
                        'mother_monthly_income' => 8000,
                        'father_status' => 'Deceased (2019)',
                        'total_family_income' => 8000,
                        'family_size' => 4,
                        'siblings' => [
                            ['name' => 'Maria', 'age' => 17, 'status' => 'Grade 12 Student'],
                            ['name' => 'Roberto', 'age' => 14, 'status' => 'Grade 8 Student']
                        ]
                    ],
                    'financial_situation' => [
                        'monthly_expenses' => [
                            'food' => 5000,
                            'utilities' => 800,
                            'education' => 2500,
                            'transportation' => 600,
                            'rent' => 2000
                        ],
                        'challenges' => 'Single mother supporting 3 children. Eldest son (applicant) works part-time to help with family expenses while studying.'
                    ],
                    'personal_statement' => 'As the eldest son, I feel responsible for helping my family while pursuing my education. I believe that completing my IT degree will provide better opportunities for our family and allow me to support my younger siblings\' education.',
                    'work_experience' => 'Part-time computer shop assistant and freelance computer repair technician'
                ]),
                'status' => 'under_review',
                'committee_notes' => 'Demonstrates clear financial need. Academic performance slightly below average but considering work-study circumstances. Pending final review of financial documents.',
                'submitted_at' => Carbon::now()->subDays(15),
                'reviewed_at' => null,
                'reviewed_by' => null,
                'created_at' => Carbon::now()->subDays(20),
                'updated_at' => Carbon::now()->subDays(15)
            ]);
        }

        // === INDIGENOUS SCHOLARSHIP APPLICATIONS ===

        // Lakambini Fernandez - Submitted
        if ($indigenousStudents->count() > 0) {
            Application::firstOrCreate([
                'scholarship_id' => $indigenousScholarship->id,
                'user_id' => $indigenousStudents->first()->id, // Lakambini Fernandez
                'application_data' => json_encode([
                    'personal_information' => [
                        'full_name' => 'Lakambini Rose F. Fernandez',
                        'date_of_birth' => '2004-05-30',
                        'place_of_birth' => 'Tinubdan, Daanbantayan, Cebu',
                        'gender' => 'Female',
                        'civil_status' => 'Single',
                        'nationality' => 'Filipino',
                        'indigenous_group' => 'Bantayan Island Native Community'
                    ],
                    'academic_information' => [
                        'current_gpa' => 3.0,
                        'school' => 'University of San Carlos',
                        'course' => 'Bachelor of Arts in Anthropology',
                        'year_level' => '1st Year',
                        'expected_graduation' => '2028-04-30'
                    ],
                    'cultural_information' => [
                        'tribal_affiliation' => 'Bantayan Island Native Community',
                        'tribal_position' => 'Youth Cultural Ambassador',
                        'indigenous_language' => 'Cebuano-Bantayanon dialect',
                        'cultural_practices' => [
                            'Traditional fishing methods',
                            'Indigenous healing practices',
                            'Traditional weaving',
                            'Cultural dances and songs',
                            'Traditional food preparation'
                        ],
                        'community_role' => 'Organizer of cultural events and traditional dance performances'
                    ],
                    'cultural_involvement' => [
                        'Traditional dance performer in community festivals',
                        'Teacher of indigenous language to children',
                        'Community cultural events organizer',
                        'Indigenous rights advocate',
                        'Cultural preservation documentation project volunteer'
                    ],
                    'family_information' => [
                        'father_name' => 'Kapitan Miguel Fernandez',
                        'father_occupation' => 'Community Leader/Traditional Fisher',
                        'mother_name' => 'Aling Rosita Fernandez',
                        'mother_occupation' => 'Traditional Healer/Weaver'
                    ],
                    'goals_and_aspirations' => [
                        'academic_goals' => 'To become an anthropologist specializing in indigenous Philippine cultures, particularly the maritime communities of the Visayas.',
                        'cultural_goals' => 'To document and preserve the traditional knowledge and practices of my community for future generations.',
                        'community_goals' => 'To establish a cultural center that will serve as a repository of indigenous knowledge and a training ground for cultural preservation.'
                    ],
                    'personal_statement' => 'Growing up in an indigenous community, I have seen how modernization threatens our traditional ways of life. Through education, I hope to bridge the gap between traditional knowledge and modern academic understanding, ensuring that our cultural heritage is preserved and respected.',
                    'research_interests' => 'Maritime anthropology, indigenous knowledge systems, cultural preservation, traditional ecological knowledge'
                ]),
                'status' => 'submitted',
                'submitted_at' => Carbon::now()->subDays(5),
                'created_at' => Carbon::now()->subDays(8),
                'updated_at' => Carbon::now()->subDays(5)
            ]);
        }

        // Create additional students for draft/rejected applications
        $this->createAdditionalStudents();

        // Create some additional draft applications for variety
        $this->createDraftApplications();

        // Create some rejected applications for complete status representation
        $this->createRejectedApplications();

        $this->command->info('Application seeder completed successfully!');
        $this->command->info('Created applications with various statuses:');
        $this->command->info('- Approved applications with complete review data');
        $this->command->info('- Under review applications with committee notes');
        $this->command->info('- Recently submitted applications');
        $this->command->info('- Draft applications in progress');
        $this->command->info('- Some rejected applications for complete data set');
    }

    /**
     * Create draft applications (applications in progress)
     */
    private function createDraftApplications(): void
    {
        $availableStudents = User::where('role', 'student')
            ->whereNotIn('id', Application::pluck('user_id'))  // ✅ Exclude existing
            ->get();

        foreach ($availableStudents as $student) {
            // Skip if student already has an application
            if (Application::where('user_id', $student->id)->exists()) {
                continue;
            }

            Application::firstOrCreate([  // ✅ Safe creation
                'scholarship_id' => $student->scholarship_id,
                'user_id' => $student->id,
            ], [
                'scholarship_id' => $student->scholarship_id,
                'user_id' => $student->id,
                'application_data' => json_encode([
                    'personal_information' => [
                        'full_name' => $student->name,
                        'date_of_birth' => '2003-06-15',
                        'gender' => 'Male',
                        'civil_status' => 'Single'
                    ],
                    'academic_information' => [
                        'current_gpa' => rand(250, 400) / 100, // 2.50 to 4.00
                        'school' => 'Local University',
                        'course' => 'Various Program',
                        'year_level' => rand(1, 4) . 'st/nd/rd/th Year'
                    ],
                    'note' => 'Application in progress - incomplete information'
                ]),
                'status' => 'draft',
                'created_at' => Carbon::now()->subDays(rand(1, 30)),
                'updated_at' => Carbon::now()->subDays(rand(1, 7))
            ]);
        }

        $this->command->info('Created draft applications with proper scholarship IDs.');
    }

    /**
     * Create rejected applications for complete status representation
     */
    private function createRejectedApplications(): void
    {
        // Get students who DON'T have applications yet
        $availableStudents = User::where('role', 'student')
            ->whereNotIn('id', Application::pluck('user_id'))
            ->get();

        if ($availableStudents->count() < 2) {
            $this->command->warn('Not enough available students for rejected applications.');
            return;
        }

        foreach ($availableStudents->take(2) as $student) {
            // Get the committee member for this student's scholarship
            $committee = User::where('role', 'committee')
                ->where('scholarship_id', $student->scholarship_id)
                ->first();

            if (!$committee) {
                $this->command->warn("No committee found for scholarship ID: {$student->scholarship_id}");
                continue;
            }

            Application::firstOrCreate([
                'scholarship_id' => $student->scholarship_id,
                'user_id' => $student->id,
            ], [
                'scholarship_id' => $student->scholarship_id,
                'user_id' => $student->id,
                'application_data' => json_encode([
                    'personal_information' => [
                        'full_name' => $student->name,
                        'gpa' => 2.1, // Below requirements
                        'course' => 'Sample Course'
                    ],
                    'issues' => [
                        'GPA below minimum requirements',
                        'Incomplete documentation',
                        'Missing required essays'
                    ]
                ]),
                'status' => 'rejected',
                'committee_notes' => 'Application rejected due to GPA below minimum requirements (2.1). Incomplete documentation submitted. Student encouraged to reapply next cycle after improving academic performance.',
                'submitted_at' => Carbon::now()->subDays(70),    // Submitted 70 days ago
                'reviewed_at' => Carbon::now()->subDays(55),     // Reviewed 55 days ago
                'reviewed_by' => $committee->id,                 // ✅ Committee reviewer assigned
                'created_at' => Carbon::now()->subDays(75),
                'updated_at' => Carbon::now()->subDays(55)
            ]);
        }

        $this->command->info('Created rejected applications with proper committee reviewers.');
    }

    /**
     * Create additional students for draft/rejected applications
     */
    private function createAdditionalStudents(): void
    {
        // Create 2-3 additional students per scholarship for draft/rejected applications
        $scholarships = Scholarship::where('is_active', true)->get();

        foreach ($scholarships as $scholarship) {
            // Create 3 more students per scholarship
            User::factory()
                ->count(3)
                ->student($scholarship->id)
                ->create();
        }

        $this->command->info('Created additional students for draft/rejected applications.');
    }
}
