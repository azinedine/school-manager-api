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
            // Pedagogical Context
            $table->string('domain')->default('')->after('subject');
            $table->string('learning_unit')->default('')->after('domain');
            $table->string('knowledge_resource')->default('')->after('learning_unit');

            // Lesson Flow
            $table->json('lesson_elements')->nullable()->after('knowledge_resource');

            // Evaluation (Discriminator)
            $table->enum('evaluation_type', ['assessment', 'homework'])->nullable()->after('lesson_elements');
            $table->longText('evaluation_content')->nullable()->after('evaluation_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lesson_preparations', function (Blueprint $table) {
            $table->dropColumn([
                'domain',
                'learning_unit',
                'knowledge_resource',
                'lesson_elements',
                'evaluation_type',
                'evaluation_content'
            ]);
        });
    }
};
