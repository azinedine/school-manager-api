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
        Schema::create('student_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('student_id')
                ->constrained('grade_students')
                ->cascadeOnDelete();

            $table->string('report_number');
            $table->string('academic_year'); // e.g., "2024-2025"
            $table->date('report_date');

            $table->text('incident_description');

            // JSON array of selected sanctions keys (e.g., ['parent_summons', 'written_warning'])
            $table->json('sanctions')->nullable();
            $table->string('other_sanction')->nullable();

            $table->string('status')->default('draft'); // draft, formalized

            // Snapshot of student data at time of report (class, name, etc.)
            // Essential for historical accuracy if student moves classes
            $table->json('meta')->nullable();

            $table->timestamps();

            // Indexes for common queries
            $table->index(['institution_id', 'academic_year']);
            $table->index('student_id');
            $table->index('teacher_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_reports');
    }
};
