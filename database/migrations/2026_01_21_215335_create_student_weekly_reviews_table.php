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
        Schema::create('student_weekly_reviews', function (Blueprint $table) {
            $table->id();
            $table->uuid('grade_student_id');
            $table->uuid('grade_class_id');
            $table->unsignedBigInteger('teacher_id');

            // Week identification (ISO week + year)
            $table->smallInteger('year'); // e.g., 2026
            $table->tinyInteger('week_number'); // 1-53 (ISO week)
            $table->date('week_start_date'); // Monday of that week (computed)

            // Review status
            $table->boolean('notebook_checked')->default(false);
            $table->boolean('lesson_written')->default(true);
            $table->boolean('homework_done')->default(true);
            $table->decimal('score', 4, 2)->nullable(); // Optional 0-20 score

            // Observation (enum stored as string for flexibility)
            $table->enum('observation_type', [
                'OK',
                'NO_NOTEBOOK',
                'LESSON_NOT_WRITTEN',
                'INCOMPLETE',
                'HOMEWORK_MISSING',
                'COMMUNICATION_NOTE',
                'MULTIPLE_ISSUES',
            ])->default('OK');
            $table->text('observation_notes')->nullable(); // Free text for details

            // Alert tracking
            $table->boolean('alert_resolved')->default(false);
            $table->timestamp('resolved_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->unique(['grade_student_id', 'year', 'week_number'], 'unique_student_week');
            $table->index(['grade_class_id', 'year', 'week_number'], 'idx_class_week');
            $table->index(['grade_class_id', 'observation_type', 'alert_resolved'], 'idx_pending_alerts');

            // Foreign keys
            $table->foreign('grade_student_id')
                ->references('id')
                ->on('grade_students')
                ->onDelete('cascade');

            $table->foreign('grade_class_id')
                ->references('id')
                ->on('grade_classes')
                ->onDelete('cascade');

            $table->foreign('teacher_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_weekly_reviews');
    }
};
