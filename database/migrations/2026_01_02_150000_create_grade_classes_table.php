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
        Schema::create('grade_classes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Teacher
            $table->string('name'); // e.g., "4AM1"
            $table->string('subject')->nullable(); // e.g., "Mathematics"
            $table->string('grade_level')->nullable(); // e.g., "4th Year Middle School"
            $table->string('academic_year'); // e.g., "2024-2025"
            $table->timestamps();

            $table->index(['user_id', 'academic_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_classes');
    }
};
