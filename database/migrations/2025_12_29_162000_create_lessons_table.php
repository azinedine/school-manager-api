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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();

            // Foreign key to institutions (school)
            $table->foreignId('institution_id')
                ->constrained('institutions')
                ->onDelete('cascade');

            // Foreign key to users (teacher)
            $table->foreignId('teacher_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Lesson Information
            $table->string('title');
            $table->longText('content')->nullable();
            $table->date('lesson_date');
            $table->string('academic_year', 20); // e.g., "2024-2025"
            $table->string('class_name', 100); // e.g., "10-A"
            $table->string('subject_name', 100); // e.g., "Mathematics"

            // Status
            $table->enum('status', ['draft', 'published'])->default('draft');

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('institution_id');
            $table->index('teacher_id');
            $table->index('lesson_date');
            $table->index('status');
            $table->index(['institution_id', 'teacher_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
