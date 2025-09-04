<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting database seeding...');

        // Create Super Admin first (before scholarships for clear separation)
        $this->createSuperAdmin();

        // Seed in proper order with dependencies
        $this->call([
            ScholarshipSeeder::class,    // 1. Create scholarships first
            UserSeeder::class,           // 2. Create users (depends on scholarships)
            ApplicationSeeder::class,    // 3. Create applications (depends on users & scholarships)
        ]);

        $this->command->info('✅ Database seeding completed successfully!');
        $this->printSummary();
    }

    /**
     * Create the super admin user
     */
    private function createSuperAdmin(): void
    {
        User::firstOrCreate([
            'email' => 'superadmin@daanbantayan.gov.ph'
        ], [
            'name' => 'Municipal Administrator',
            'password' => Hash::make('superadmin123'),
            'role' => 'admin',
            'scholarship_id' => null, // Super admin has no scholarship affiliation
            'profile_data' => json_encode([
                'position' => 'Municipal Administrator',
                'department' => 'Office of the Municipal Mayor',
                'contact_number' => '+63 999 000 0000',
                'employee_id' => 'MA-2024-001',
                'access_level' => 'super_admin',
                'responsibilities' => [
                    'System administration',
                    'User management',
                    'Scholarship program oversight',
                    'Report generation'
                ]
            ])
        ]);

        $this->command->info('✅ Super Admin created');
    }

    /**
     * Print summary of seeded data
     */
    private function printSummary(): void
    {
        $this->command->info('');
        $this->command->info('📊 Seeding Summary:');

        // Count scholarships
        $scholarshipCount = \App\Models\Scholarship::count();
        $this->command->info("   🎓 Scholarships: {$scholarshipCount}");

        // Count users by role
        $adminCount = User::where('role', 'admin')->count();
        $committeeCount = User::where('role', 'committee')->count();
        $studentCount = User::where('role', 'student')->count();
        $totalUsers = User::count();

        $this->command->info("   👥 Users: {$totalUsers} total");
        $this->command->info("      🔑 Admins: {$adminCount}");
        $this->command->info("      👨‍💼 Committee: {$committeeCount}");
        $this->command->info("      🎓 Students: {$studentCount}");

        // Count applications
        $applicationCount = \App\Models\Application::count();
        $this->command->info("   📄 Applications: {$applicationCount}");

        // Show login credentials
        $this->command->info('');
        $this->command->info('🔐 Login Credentials:');
        $this->command->info('   Super Admin: superadmin@daanbantayan.gov.ph / superadmin123');
        $this->command->info('   Committee: [role].committee.chair@daanbantayan.gov.ph / committee123');
        $this->command->info('   Students: [name]@student.daanbantayan.edu.ph / student123');
    }
}
