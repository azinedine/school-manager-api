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
        Schema::create('grade_students', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('grade_class_id');
            $table->string('student_number')->nullable(); // External ID (e.g., matricule)
            $table->string('last_name');
            $table->string('first_name');
            $table->date('date_of_birth')->nullable();
            $table->string('special_case')->nullable(); // e.g., "autism", "exemption", "transfer"
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('grade_class_id')
                ->references('id')
                ->on('grade_classes')
                ->onDelete('cascade');

            $table->index('grade_class_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_students');
    }
};
