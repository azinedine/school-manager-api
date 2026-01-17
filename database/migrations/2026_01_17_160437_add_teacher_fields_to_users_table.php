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
            $table->string('teacher_id')->nullable()->after('phone');
            $table->string('employment_status')->nullable()->after('years_of_experience');
            $table->integer('weekly_teaching_load')->nullable()->after('employment_status');
            $table->json('assigned_classes')->nullable()->after('weekly_teaching_load');
            $table->json('groups')->nullable()->after('assigned_classes');
            $table->string('avatar')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'teacher_id',
                'employment_status',
                'weekly_teaching_load',
                'assigned_classes',
                'groups',
                'avatar',
            ]);
        });
    }
};
