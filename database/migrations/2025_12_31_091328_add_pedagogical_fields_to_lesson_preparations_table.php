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
        Schema::table('lesson_preparations', function (Blueprint $table) {
            $table->json('targeted_knowledge')->nullable()->after('lesson_elements');
            $table->json('used_materials')->nullable()->after('targeted_knowledge');
            $table->json('references')->nullable()->after('used_materials');
            $table->json('phases')->nullable()->after('references');
            $table->json('activities')->nullable()->after('phases');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lesson_preparations', function (Blueprint $table) {
            $table->dropColumn([
                'targeted_knowledge',
                'used_materials',
                'references',
                'phases',
                'activities'
            ]);
        });
    }
};
