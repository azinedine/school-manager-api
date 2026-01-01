<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SuperAdminSeeder extends Seeder
{
    /**
     * Seed a Super Admin user.
     * 
     * Run with: php artisan db:seed --class=SuperAdminSeeder
     * 
     * For security, configure these in your .env file:
     * SUPER_ADMIN_NAME=Super Admin
     * SUPER_ADMIN_EMAIL=admin@example.com
     * SUPER_ADMIN_PASSWORD=your-secure-password
     */
    public function run(): void
    {
        $email = env('SUPER_ADMIN_EMAIL', 'superadmin@school.com');
        
        // Check if super admin already exists
        if (User::where('email', $email)->exists()) {
            $this->command->info("Super Admin with email {$email} already exists. Skipping.");
            return;
        }

        $user = User::create([
            'name' => env('SUPER_ADMIN_NAME', 'Super Admin'),
            'email' => $email,
            'password' => env('SUPER_ADMIN_PASSWORD', 'SuperAdmin@123!'),
            'role' => User::ROLE_SUPER_ADMIN,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);

        $this->command->info("✅ Super Admin created successfully!");
        $this->command->info("   Email: {$user->email}");
        $this->command->info("   Role: {$user->role}");
        $this->command->warn("   ⚠️  Remember to change the default password immediately!");
    }
}
