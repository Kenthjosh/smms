<?php

namespace Database\Seeders;

use App\Models\Scholarship;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScholarshipSeeder extends Seeder
{
    public function run(): void
    {
        // Merit-Based Academic Scholarship
        Scholarship::firstOrCreate([
            'slug' => 'merit-scholarship'
        ], [
            'name' => 'Merit-Based Academic Scholarship',
            'type' => 'merit',
            'description' => 'For students with outstanding academic performance and achievements. This scholarship recognizes academic excellence and promotes educational advancement for deserving students in Daanbantayan.',
            'settings' => json_encode([ // Manually encode to JSON string
                'requirements' => [
                    'minimum_gpa' => 3.5,
                    'class_rank' => 'Top 10%',
                    'standardized_test_score' => 'Required'
                ],
                'required_documents' => [
                    'official_transcript',
                    'recommendation_letter_teacher',
                    'recommendation_letter_principal',
                    'personal_essay',
                    'certificate_of_awards',
                    'birth_certificate',
                    'id_photo'
                ],
                'essay_topics' => [
                    'Academic goals and career aspirations',
                    'How this scholarship will impact your education',
                    'Community involvement and leadership experiences'
                ],
                'selection_criteria' => [
                    'Academic Performance (40%)',
                    'Essays and Personal Statement (30%)',
                    'Extracurricular Activities (20%)',
                    'Letters of Recommendation (10%)'
                ]
            ]),
            'is_active' => true,
            'application_deadline' => now()->addMonths(6)->format('Y-m-d')
        ]);

        // Sports Excellence Scholarship
        Scholarship::firstOrCreate([
            'slug' => 'sports-scholarship'
        ], [
            'name' => 'Athletic Excellence Scholarship',
            'type' => 'sports',
            'description' => 'For student-athletes who demonstrate exceptional athletic abilities and sportsmanship. This scholarship supports talented athletes from Daanbantayan in pursuing both athletic and academic excellence.',
            'settings' => json_encode([
                'requirements' => [
                    'minimum_gpa' => 2.75,
                    'athletic_participation' => 'At least 2 years',
                    'physical_fitness' => 'Medical clearance required'
                ],
                'sports_categories' => [
                    'Basketball',
                    'Volleyball',
                    'Track and Field',
                    'Swimming',
                    'Badminton',
                    'Football',
                    'Sepak Takraw',
                    'Boxing'
                ],
                'required_documents' => [
                    'athletic_records',
                    'coach_recommendation',
                    'medical_clearance',
                    'sports_certificates',
                    'official_transcript',
                    'personal_statement',
                    'birth_certificate',
                    'id_photo'
                ],
                'selection_criteria' => [
                    'Athletic Performance (50%)',
                    'Academic Performance (25%)',
                    'Character and Sportsmanship (15%)',
                    'Coach Recommendations (10%)'
                ]
            ]),
            'is_active' => true,
            'application_deadline' => now()->addMonths(4)->format('Y-m-d')
        ]);

        // Need-Based Financial Scholarship
        Scholarship::firstOrCreate([
            'slug' => 'need-based-scholarship'
        ], [
            'name' => 'Financial Need Scholarship',
            'type' => 'need-based',
            'description' => 'For students from low-income families who demonstrate financial need. This scholarship aims to provide educational opportunities for economically disadvantaged but academically capable students from Daanbantayan.',
            'settings' => json_encode([
                'requirements' => [
                    'maximum_family_income' => 50000,
                    'minimum_gpa' => 2.5,
                    'residency' => 'Daanbantayan resident for at least 2 years'
                ],
                'income_brackets' => [
                    'Extremely Low Income' => '0 - 15,000',
                    'Very Low Income' => '15,001 - 30,000',
                    'Low Income' => '30,001 - 50,000'
                ],
                'required_documents' => [
                    'certificate_of_indigency',
                    'family_income_certificate',
                    'barangay_certificate',
                    'birth_certificates_family',
                    'official_transcript',
                    'personal_statement',
                    'parent_employment_certificate',
                    'utility_bills',
                    'id_photo'
                ],
                'assessment_factors' => [
                    'Family Income (40%)',
                    'Academic Performance (30%)',
                    'Financial Need Assessment (20%)',
                    'Community Involvement (10%)'
                ]
            ]),
            'is_active' => true,
            'application_deadline' => now()->addMonths(5)->format('Y-m-d')
        ]);

        // Indigenous Peoples Scholarship
        Scholarship::firstOrCreate([
            'slug' => 'indigenous-scholarship'
        ], [
            'name' => 'Indigenous Peoples Education Scholarship',
            'type' => 'indigenous',
            'description' => 'Dedicated to supporting indigenous students from Daanbantayan in accessing higher education while preserving their cultural heritage and identity.',
            'settings' => json_encode([
                'requirements' => [
                    'indigenous_community_certification' => 'Required',
                    'minimum_gpa' => 2.0,
                    'cultural_involvement' => 'Active participation in community activities'
                ],
                'recognized_groups' => [
                    'Cebuano Indigenous Communities',
                    'Bantayan Island Native Groups',
                    'Traditional Fisher Communities'
                ],
                'required_documents' => [
                    'indigenous_certification',
                    'tribal_leader_endorsement',
                    'cultural_portfolio',
                    'official_transcript',
                    'personal_narrative',
                    'community_involvement_proof',
                    'birth_certificate',
                    'id_photo'
                ],
                'special_provisions' => [
                    'Cultural sensitivity in application process',
                    'Flexible documentation requirements',
                    'Community-based assessment options'
                ]
            ]),
            'is_active' => true,
            'application_deadline' => now()->addMonths(7)->format('Y-m-d')
        ]);

        $this->command->info('Scholarship seeder completed successfully!');
        $this->command->info('Created 4 scholarship programs: Merit, Sports, Need-based, and Indigenous');
    }
}
