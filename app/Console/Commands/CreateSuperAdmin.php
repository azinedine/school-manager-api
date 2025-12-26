<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create 
                            {--name= : The name of the super admin}
                            {--email= : The email address}
                            {--password= : The password (will prompt if not provided)}
                            {--force : Overwrite if user already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Super Admin user account';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔐 Creating Super Admin Account');
        $this->newLine();

        // Gather input
        $name = $this->option('name') ?? $this->ask('Enter admin name', 'Super Admin');
        $email = $this->option('email') ?? $this->ask('Enter admin email');
        $password = $this->option('password') ?? $this->secret('Enter admin password (min 8 characters)');

        // Validate input
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error("❌ {$error}");
            }
            return Command::FAILURE;
        }

        // Check if user exists
        $existingUser = User::where('email', $email)->first();
        
        if ($existingUser) {
            if (!$this->option('force')) {
                $this->error("❌ A user with email {$email} already exists.");
                $this->info("   Use --force to update the existing user to super_admin.");
                return Command::FAILURE;
            }

            // Update existing user to super_admin
            $existingUser->update([
                'name' => $name,
                'password' => Hash::make($password),
                'role' => 'super_admin',
            ]);

            $this->info("✅ Existing user updated to Super Admin!");
            $this->table(
                ['Field', 'Value'],
                [
                    ['Name', $existingUser->name],
                    ['Email', $existingUser->email],
                    ['Role', $existingUser->role],
                    ['Updated At', $existingUser->updated_at->toDateTimeString()],
                ]
            );
            
            return Command::SUCCESS;
        }

        // Create new super admin
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'super_admin',
            'email_verified_at' => now(),
        ]);

        $this->newLine();
        $this->info("✅ Super Admin created successfully!");
        $this->table(
            ['Field', 'Value'],
            [
                ['ID', $user->id],
                ['Name', $user->name],
                ['Email', $user->email],
                ['Role', $user->role],
                ['Created At', $user->created_at->toDateTimeString()],
            ]
        );

        $this->newLine();
        $this->warn("⚠️  Store these credentials securely. The password cannot be recovered.");

        return Command::SUCCESS;
    }
}
