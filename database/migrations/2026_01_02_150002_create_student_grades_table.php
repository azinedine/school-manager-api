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
        Schema::create('student_grades', function (Blueprint $table) {
            $table->id();
            $table->uuid('grade_student_id');
            $table->tinyInteger('term'); // 1, 2, or 3
            $table->decimal('behavior', 3, 1)->default(5); // 0-5
            $table->decimal('applications', 3, 1)->default(5); // 0-5
            $table->decimal('notebook', 3, 1)->default(5); // 0-5
            $table->decimal('assignment', 4, 2)->default(0); // 0-20
            $table->decimal('exam', 4, 2)->default(0); // 0-20
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
        Schema::dropIfExists('student_grades');
    }
};
