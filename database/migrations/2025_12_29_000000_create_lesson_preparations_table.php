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
        Schema::create('lesson_preparations', function (Blueprint $table) {
            $table->id();

            // Foreign key to users (teacher)
            $table->foreignId('teacher_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Basic Information
            $table->string('title');
            $table->string('subject');
            $table->string('class');
            $table->date('date');
            $table->unsignedInteger('duration_minutes')->default(45);

            // Content (stored as JSON)
            $table->json('learning_objectives')->nullable();
            $table->longText('description')->nullable();
            $table->json('key_topics')->nullable();

            // Methodology (stored as JSON)
            $table->json('teaching_methods')->nullable();
            $table->json('resources_needed')->nullable();

            // Assessment (stored as JSON)
            $table->json('assessment_methods')->nullable();
            $table->longText('assessment_criteria')->nullable();

            // Additional
            $table->longText('notes')->nullable();
            $table->enum('status', ['draft', 'ready', 'delivered'])->default('draft');

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('teacher_id');
            $table->index('date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_preparations');
    }
};
