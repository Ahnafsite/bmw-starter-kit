<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@bmw-pmb.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        // Create Verificator User
        $verificator = User::create([
            'name' => 'Verificator User',
            'email' => 'verificator@bmw-pmb.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $verificator->assignRole('verificator');

        // Create Default Applicant Users
        $applicant1 = User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $applicant1->assignRole('applicant');

        $applicant2 = User::create([
            'name' => 'Jane Smith',
            'email' => 'jane.smith@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $applicant2->assignRole('applicant');

        $applicant3 = User::create([
            'name' => 'Mike Johnson',
            'email' => 'mike.johnson@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $applicant3->assignRole('applicant');

        // Create Test User (for testing purposes)
        $testUser = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $testUser->assignRole('applicant'); // Default role
    }
}
