<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->json('permissions')->nullable();
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        // Seed default system roles
        DB::table('roles')->insert([
            ['name' => 'super_admin', 'display_name' => 'Super Administrator', 'description' => 'Full system access', 'permissions' => null, 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'admin', 'display_name' => 'Administrator', 'description' => 'School administrator', 'permissions' => null, 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'teacher', 'display_name' => 'Teacher', 'description' => 'Teaching staff', 'permissions' => null, 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'student', 'display_name' => 'Student', 'description' => 'Student user', 'permissions' => null, 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'parent', 'display_name' => 'Parent', 'description' => 'Parent/Guardian', 'permissions' => null, 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
