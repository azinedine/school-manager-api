<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed an Admin user.
     *
     * Run with: php artisan db:seed --class=AdminUserSeeder
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@school.com');

        // Check if admin already exists
        if (User::where('email', $email)->exists()) {
            $this->command->info("Admin with email {$email} already exists. Skipping.");

            return;
        }

        $user = User::create([
            'name' => env('ADMIN_NAME', 'Admin User'),
            'email' => $email,
            'password' => env('ADMIN_PASSWORD', 'password'), // Will be hashed by model cast if properly set, or we rely on model cast
            'role' => User::ROLE_ADMIN,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);

        $this->command->info('✅ Admin created successfully!');
        $this->command->info("   Email: {$user->email}");
        $this->command->info("   Role: {$user->role}");
        $this->command->warn('   ⚠️  Remember to change the default password immediately!');
    }
}
