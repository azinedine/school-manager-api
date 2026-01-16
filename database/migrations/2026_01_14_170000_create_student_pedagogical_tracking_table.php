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
        Schema::create('student_pedagogical_tracking', function (Blueprint $table) {
            $table->id();
            $table->uuid('grade_student_id');
            $table->tinyInteger('term'); // 1, 2, or 3
            $table->boolean('oral_interrogation')->default(false);
            $table->boolean('notebook_checked')->default(false);
            $table->timestamp('last_interrogation_at')->nullable();
            $table->timestamp('last_notebook_check_at')->nullable();
            $table->timestamps();

            $table->foreign('grade_student_id')
                ->references('id')
                ->on('grade_students')
                ->onDelete('cascade');

            $table->unique(['grade_student_id', 'term']);
            $table->index('grade_student_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_pedagogical_tracking');
    }
};
