<?php

namespace Database\Seeders;

use App\Models\Scholarship;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

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

        // Create 2-3 additional students per scholarship for draft/rejected applications
        $scholarships = Scholarship::all();

        foreach ($scholarships as $scholarship) {
            // Create 3 more students per scholarship
            User::factory()
                ->count(50)
                ->student($scholarship->id)
                ->create();
        }

        $this->call([
            ScholarshipSeeder::class,
            UserSeeder::class,
            ApplicationSeeder::class,
        ]);
    }
}
