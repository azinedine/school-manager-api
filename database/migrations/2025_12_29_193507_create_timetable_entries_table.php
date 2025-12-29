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
        Schema::create('timetable_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('day', ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']);
            $table->string('time_slot');
            $table->string('class');
            $table->enum('mode', ['fullClass', 'groups'])->default('fullClass');
            $table->string('group')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timetable_entries');
    }
};
