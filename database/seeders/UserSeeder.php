<?php

namespace Database\Seeders;

use App\Models\Scholarship;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Get scholarships (they should exist from ScholarshipSeeder)
        $meritScholarship = Scholarship::where('slug', 'merit-scholarship')->first();
        $sportsScholarship = Scholarship::where('slug', 'sports-scholarship')->first();
        $needBasedScholarship = Scholarship::where('slug', 'need-based-scholarship')->first();
        $indigenousScholarship = Scholarship::where('slug', 'indigenous-scholarship')->first();

        // Check if scholarships exist
        if (!$meritScholarship || !$sportsScholarship || !$needBasedScholarship || !$indigenousScholarship) {
            $this->command->error('Scholarships not found! Please run ScholarshipSeeder first.');
            return;
        }

        // SUPER ADMIN - NO SCHOLARSHIP AFFILIATION
        User::firstOrCreate([
            'email' => 'superadmin@daanbantayan.gov.ph'
        ], [
            'name' => 'Municipal Administrator',
            'password' => Hash::make('superadmin123'),
            'role' => 'admin',
            'scholarship_id' => null, // Super admin is not linked to any specific scholarship
            'profile_data' => json_encode([
                'position' => 'Municipal Administrator',
                'department' => 'Office of the Municipal Mayor',
                'contact_number' => '+63 999 000 0000',
                'employee_id' => 'MA-2024-001',
                'access_level' => 'super_admin'
            ])
        ]);

        // MERIT SCHOLARSHIP COMMITTEE MEMBERS
        User::firstOrCreate([
            'email' => 'merit.committee.chair@daanbantayan.gov.ph'
        ], [
            'name' => 'Dr. Maria Elena Santos',
            'password' => Hash::make('committee123'),
            'role' => 'committee',
            'scholarship_id' => $meritScholarship->id,
            'profile_data' => json_encode([
                'position' => 'Committee Chairperson - Merit Scholarship',
                'department' => 'Municipal Education Office',
                'contact_number' => '+63 999 111 1111',
                'employee_id' => 'MEO-2024-001',
                'expertise' => 'Academic Excellence Evaluation',
                'education' => 'PhD in Education Administration'
            ])
        ]);

        User::firstOrCreate([
            'email' => 'merit.committee.member@daanbantayan.gov.ph'
        ], [
            'name' => 'Prof. Juan Carlos Dela Cruz',
            'password' => Hash::make('committee123'),
            'role' => 'committee',
            'scholarship_id' => $meritScholarship->id,
            'profile_data' => json_encode([
                'position' => 'Committee Member - Merit Scholarship',
                'department' => 'Municipal Education Office',
                'contact_number' => '+63 999 111 2222',
                'employee_id' => 'MEO-2024-002',
                'expertise' => 'Student Academic Assessment',
                'education' => 'Master in Educational Psychology'
            ])
        ]);

        // SPORTS SCHOLARSHIP COMMITTEE MEMBERS
        User::firstOrCreate([
            'email' => 'sports.committee.chair@daanbantayan.gov.ph'
        ], [
            'name' => 'Coach Roberto "Bobby" Alvarez',
            'password' => Hash::make('committee123'),
            'role' => 'committee',
            'scholarship_id' => $sportsScholarship->id,
            'profile_data' => json_encode([
                'position' => 'Committee Chairperson - Sports Scholarship',
                'department' => 'Municipal Sports Development Office',
                'contact_number' => '+63 999 222 1111',
                'employee_id' => 'MSDO-2024-001',
                'expertise' => 'Athletic Performance Evaluation',
                'sports_background' => 'Former National Basketball Team Member',
                'certifications' => ['Level 3 Basketball Coach', 'Sports Psychology Certificate']
            ])
        ]);

        User::firstOrCreate([
            'email' => 'sports.committee.member@daanbantayan.gov.ph'
        ], [
            'name' => 'Ms. Elena Marie Rodriguez',
            'password' => Hash::make('committee123'),
            'role' => 'committee',
            'scholarship_id' => $sportsScholarship->id,
            'profile_data' => json_encode([
                'position' => 'Committee Member - Sports Scholarship',
                'department' => 'Municipal Sports Development Office',
                'contact_number' => '+63 999 222 2222',
                'employee_id' => 'MSDO-2024-002',
                'expertise' => 'Sports Medicine and Athlete Welfare',
                'education' => 'BS Sports Science, MS Sports Medicine'
            ])
        ]);

        // NEED-BASED SCHOLARSHIP COMMITTEE MEMBERS
        User::firstOrCreate([
            'email' => 'needbased.committee.chair@daanbantayan.gov.ph'
        ], [
            'name' => 'Ms. Carmen Isabel Villanueva',
            'password' => Hash::make('committee123'),
            'role' => 'committee',
            'scholarship_id' => $needBasedScholarship->id,
            'profile_data' => json_encode([
                'position' => 'Committee Chairperson - Need-Based Scholarship',
                'department' => 'Municipal Social Welfare and Development Office',
                'contact_number' => '+63 999 333 1111',
                'employee_id' => 'MSWDO-2024-001',
                'expertise' => 'Socio-Economic Assessment and Family Welfare',
                'education' => 'Master in Social Work'
            ])
        ]);

        User::firstOrCreate([
            'email' => 'needbased.committee.member@daanbantayan.gov.ph'
        ], [
            'name' => 'Mr. Ricardo "Rico" Tan',
            'password' => Hash::make('committee123'),
            'role' => 'committee',
            'scholarship_id' => $needBasedScholarship->id,
            'profile_data' => json_encode([
                'position' => 'Committee Member - Need-Based Scholarship',
                'department' => 'Municipal Social Welfare and Development Office',
                'contact_number' => '+63 999 333 2222',
                'employee_id' => 'MSWDO-2024-002',
                'expertise' => 'Financial Assessment and Documentation Review',
                'education' => 'BS Social Work, Certificate in Poverty Assessment'
            ])
        ]);

        // INDIGENOUS SCHOLARSHIP COMMITTEE MEMBERS
        User::firstOrCreate([
            'email' => 'indigenous.committee.chair@daanbantayan.gov.ph'
        ], [
            'name' => 'Kapitan Rosa Marie Fernandez',
            'password' => Hash::make('committee123'),
            'role' => 'committee',
            'scholarship_id' => $indigenousScholarship->id,
            'profile_data' => json_encode([
                'position' => 'Committee Chairperson - Indigenous Scholarship',
                'department' => 'Municipal Indigenous Peoples Affairs Office',
                'contact_number' => '+63 999 444 1111',
                'employee_id' => 'MIPAO-2024-001',
                'expertise' => 'Indigenous Community Relations and Cultural Preservation',
                'cultural_background' => 'Indigenous Community Leader'
            ])
        ]);

        // SAMPLE STUDENTS FOR EACH SCHOLARSHIP TYPE

        // Merit Scholarship Students
        User::firstOrCreate([
            'email' => 'anna.gonzalez@student.daanbantayan.edu.ph'
        ], [
            'name' => 'Anna Marie T. Gonzalez',
            'password' => Hash::make('student123'),
            'role' => 'student',
            'scholarship_id' => $meritScholarship->id,
            'profile_data' => json_encode([
                'student_id' => 'MS-2024-001',
                'school' => 'University of San Carlos',
                'course' => 'Bachelor of Science in Computer Science',
                'year_level' => '3rd Year',
                'gpa' => 3.92,
                'class_rank' => '2 of 150',
                'contact_number' => '+63 999 501 1111',
                'address' => 'Purok 3, Barangay Poblacion, Daanbantayan, Cebu',
                'parent_guardian' => 'Mr. Antonio Gonzalez & Mrs. Maria Gonzalez',
                'parent_contact' => '+63 999 501 2222',
                'achievements' => [
                    'Dean\'s List (6 consecutive semesters)',
                    'Outstanding Student in Computer Science 2023',
                    'Programming Competition Champion - Regional Level'
                ]
            ])
        ]);

        User::firstOrCreate([
            'email' => 'john.torres@student.daanbantayan.edu.ph'
        ], [
            'name' => 'John Michael L. Torres',
            'password' => Hash::make('student123'),
            'role' => 'student',
            'scholarship_id' => $meritScholarship->id,
            'profile_data' => json_encode([
                'student_id' => 'MS-2024-002',
                'school' => 'Cebu Institute of Technology - University',
                'course' => 'Bachelor of Science in Civil Engineering',
                'year_level' => '2nd Year',
                'gpa' => 3.85,
                'class_rank' => '5 of 120',
                'contact_number' => '+63 999 502 1111',
                'address' => 'Sitio Lawis, Barangay Maya, Daanbantayan, Cebu',
                'parent_guardian' => 'Mr. Eduardo Torres & Mrs. Luz Torres',
                'parent_contact' => '+63 999 502 2222',
                'achievements' => [
                    'Consistent Honor Student',
                    'Math Olympiad Regional Qualifier',
                    'Student Council Academic Affairs Officer'
                ]
            ])
        ]);

        // Sports Scholarship Students
        User::firstOrCreate([
            'email' => 'miguel.reyes@student.daanbantayan.edu.ph'
        ], [
            'name' => 'Miguel Angelo B. Reyes',
            'password' => Hash::make('student123'),
            'role' => 'student',
            'scholarship_id' => $sportsScholarship->id,
            'profile_data' => json_encode([
                'student_id' => 'SS-2024-001',
                'school' => 'University of the Philippines Cebu',
                'course' => 'Bachelor of Science in Sports Science',
                'year_level' => '4th Year',
                'gpa' => 3.2,
                'contact_number' => '+63 999 503 1111',
                'address' => 'Barangay Malingin, Daanbantayan, Cebu',
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
                ],
                'parent_guardian' => 'Mr. Roberto Reyes & Mrs. Carmen Reyes',
                'parent_contact' => '+63 999 503 2222'
            ])
        ]);

        User::firstOrCreate([
            'email' => 'sofia.cruz@student.daanbantayan.edu.ph'
        ], [
            'name' => 'Sofia Isabella C. Cruz',
            'password' => Hash::make('student123'),
            'role' => 'student',
            'scholarship_id' => $sportsScholarship->id,
            'profile_data' => json_encode([
                'student_id' => 'SS-2024-002',
                'school' => 'Cebu Normal University',
                'course' => 'Bachelor of Physical Education',
                'year_level' => '1st Year',
                'gpa' => 3.5,
                'contact_number' => '+63 999 504 1111',
                'address' => 'Barangay Tapilon, Daanbantayan, Cebu',
                'sport' => 'Volleyball',
                'position' => 'Setter',
                'achievements' => [
                    'Provincial Volleyball Championship Best Setter 2023',
                    'Inter-school Competition MVP',
                    'Regional Team Representative'
                ],
                'athletic_records' => [
                    'Setting accuracy: 92%',
                    'Service aces per set: 2.3',
                    'Team win percentage when playing: 78%'
                ],
                'parent_guardian' => 'Mr. Luis Cruz & Mrs. Andrea Cruz',
                'parent_contact' => '+63 999 504 2222'
            ])
        ]);

        // Need-Based Scholarship Students
        User::firstOrCreate([
            'email' => 'maria.lopez@student.daanbantayan.edu.ph'
        ], [
            'name' => 'Maria Theresa S. Lopez',
            'password' => Hash::make('student123'),
            'role' => 'student',
            'scholarship_id' => $needBasedScholarship->id,
            'profile_data' => json_encode([
                'student_id' => 'NB-2024-001',
                'school' => 'Cebu Doctors\' University',
                'course' => 'Bachelor of Science in Nursing',
                'year_level' => '2nd Year',
                'gpa' => 3.1,
                'contact_number' => '+63 999 505 1111',
                'address' => 'Sitio Kawayan, Barangay Carnaza, Daanbantayan, Cebu',
                'family_composition' => [
                    'Father: Pedro Lopez (Fisherman)',
                    'Mother: Rosa Lopez (Housewife)',
                    'Siblings: 4 (Ages 8, 12, 16, 20)'
                ],
                'family_income' => 18000,
                'parent_occupation' => 'Fisherman / Housewife',
                'parent_guardian' => 'Mr. Pedro Lopez & Mrs. Rosa Lopez',
                'parent_contact' => '+63 999 505 2222',
                'financial_situation' => 'Father is the sole breadwinner working as a fisherman. Family struggles with educational expenses for 5 children.'
            ])
        ]);

        User::firstOrCreate([
            'email' => 'jose.martinez@student.daanbantayan.edu.ph'
        ], [
            'name' => 'Jose Gabriel P. Martinez',
            'password' => Hash::make('student123'),
            'role' => 'student',
            'scholarship_id' => $needBasedScholarship->id,
            'profile_data' => json_encode([
                'student_id' => 'NB-2024-002',
                'school' => 'Cebu Technological University',
                'course' => 'Bachelor of Science in Information Technology',
                'year_level' => '3rd Year',
                'gpa' => 2.8,
                'contact_number' => '+63 999 506 1111',
                'address' => 'Purok 5, Barangay Logon, Daanbantayan, Cebu',
                'family_composition' => [
                    'Mother: Single parent - Ana Martinez (Market Vendor)',
                    'Siblings: 2 (Ages 14, 17)'
                ],
                'family_income' => 12000,
                'parent_occupation' => 'Market Vendor (Single Mother)',
                'parent_guardian' => 'Mrs. Ana Martinez (Single Mother)',
                'parent_contact' => '+63 999 506 2222',
                'financial_situation' => 'Single mother supporting 3 children through small market vending business.'
            ])
        ]);

        // Indigenous Scholarship Students
        User::firstOrCreate([
            'email' => 'lakambini.fernandez@student.daanbantayan.edu.ph'
        ], [
            'name' => 'Lakambini Rose F. Fernandez',
            'password' => Hash::make('student123'),
            'role' => 'student',
            'scholarship_id' => $indigenousScholarship->id,
            'profile_data' => json_encode([
                'student_id' => 'IP-2024-001',
                'school' => 'University of San Carlos',
                'course' => 'Bachelor of Arts in Anthropology',
                'year_level' => '1st Year',
                'gpa' => 3.0,
                'contact_number' => '+63 999 507 1111',
                'address' => 'Indigenous Community, Barangay Tinubdan, Daanbantayan, Cebu',
                'indigenous_group' => 'Bantayan Island Native Community',
                'cultural_involvement' => [
                    'Traditional dance performer',
                    'Community cultural events organizer',
                    'Indigenous language preservation advocate'
                ],
                'tribal_position' => 'Youth Cultural Ambassador',
                'parent_guardian' => 'Kapitan Miguel Fernandez & Aling Rosita Fernandez',
                'parent_contact' => '+63 999 507 2222',
                'goals' => 'To preserve indigenous culture while pursuing higher education and eventually serve the community as a cultural researcher.'
            ])
        ]);

        $this->command->info('User seeder completed successfully!');
        $this->command->info('');
        $this->command->info('=== LOGIN CREDENTIALS ===');
        $this->command->info('SUPER ADMIN (No Scholarship Affiliation):');
        $this->command->info('  Email: superadmin@daanbantayan.gov.ph');
        $this->command->info('  Password: superadmin123');
        $this->command->info('');
        $this->command->info('COMMITTEE MEMBERS:');
        $this->command->info('  Merit: merit.committee.chair@daanbantayan.gov.ph / committee123');
        $this->command->info('  Sports: sports.committee.chair@daanbantayan.gov.ph / committee123');
        $this->command->info('  Need-based: needbased.committee.chair@daanbantayan.gov.ph / committee123');
        $this->command->info('  Indigenous: indigenous.committee.chair@daanbantayan.gov.ph / committee123');
        $this->command->info('');
        $this->command->info('STUDENTS (Sample accounts):');
        $this->command->info('  Merit: anna.gonzalez@student.daanbantayan.edu.ph / student123');
        $this->command->info('  Sports: miguel.reyes@student.daanbantayan.edu.ph / student123');
        $this->command->info('  Need-based: maria.lopez@student.daanbantayan.edu.ph / student123');
        $this->command->info('  Indigenous: lakambini.fernandez@student.daanbantayan.edu.ph / student123');
    }
}
