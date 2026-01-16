<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('student'); // admin, teacher, student, parent
            $table->string('wilaya')->nullable();
            $table->string('municipality')->nullable();

            // Foreign key to institutions
            $table->foreignId('institution_id')->nullable()->constrained('institutions')->nullOnDelete();

            // Student specific
            $table->string('class')->nullable();
            $table->string('linked_student_id')->nullable(); // For parents

            // Teacher specific
            $table->json('subjects')->nullable();
            $table->json('levels')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
