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
            // New Identity Fields (if not replacing existing ones)
            if (!Schema::hasColumn('lesson_preparations', 'lesson_number')) {
                $table->string('lesson_number')->after('teacher_id')->nullable();
            }
            if (!Schema::hasColumn('lesson_preparations', 'level')) {
                $table->string('level')->after('subject')->nullable();
            }

            // Pedagogical Context
            if (!Schema::hasColumn('lesson_preparations', 'domain')) {
                $table->string('domain')->after('description')->nullable();
            }
            if (!Schema::hasColumn('lesson_preparations', 'learning_unit')) {
                $table->string('learning_unit')->after('domain')->nullable();
            }
            if (!Schema::hasColumn('lesson_preparations', 'knowledge_resource')) {
                $table->string('knowledge_resource')->after('learning_unit')->nullable();
            }

            // Expanded Content & Methodology
            if (!Schema::hasColumn('lesson_preparations', 'lesson_elements')) {
                $table->json('lesson_elements')->after('knowledge_resource')->nullable();
            }
            if (!Schema::hasColumn('lesson_preparations', 'targeted_knowledge')) {
                $table->json('targeted_knowledge')->after('lesson_elements')->nullable();
            }
            if (!Schema::hasColumn('lesson_preparations', 'used_materials')) {
                $table->json('used_materials')->after('targeted_knowledge')->nullable();
            }
            if (!Schema::hasColumn('lesson_preparations', 'references')) {
                $table->json('references')->after('used_materials')->nullable();
            }
            
            // Lesson Flow (Phases)
            if (!Schema::hasColumn('lesson_preparations', 'phases')) {
                $table->json('phases')->after('teaching_methods')->nullable();
            }
            if (!Schema::hasColumn('lesson_preparations', 'activities')) {
                $table->json('activities')->after('phases')->nullable();
            }

            // Evaluation Enhancements
            if (!Schema::hasColumn('lesson_preparations', 'evaluation_type')) {
                $table->string('evaluation_type')->after('assessment_criteria')->nullable();
            }
            if (!Schema::hasColumn('lesson_preparations', 'evaluation_content')) {
                $table->longText('evaluation_content')->after('evaluation_type')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lesson_preparations', function (Blueprint $table) {
            $table->dropColumn([
                'lesson_number',
                'level',
                'domain',
                'learning_unit',
                'knowledge_resource',
                'lesson_elements',
                'targeted_knowledge',
                'used_materials',
                'references',
                'phases',
                'activities',
                'evaluation_type',
                'evaluation_content'
            ]);
        });
    }
};
