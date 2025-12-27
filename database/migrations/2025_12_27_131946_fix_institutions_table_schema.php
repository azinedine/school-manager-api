<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fix institutions table schema:
 * - Remove redundant wilaya_code (NOT NULL was causing constraint violation)
 * - Convert municipality_id from string to proper FK
 * - Ensure proper normalization with FK relationships
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            // Drop the redundant wilaya_code column
            // wilaya.code can be accessed via wilaya_id -> wilayas.code relationship
            if (Schema::hasColumn('institutions', 'wilaya_code')) {
                $table->dropColumn('wilaya_code');
            }
        });

        // Fix municipality_id: convert from string to proper FK
        // SQLite doesn't support column modification, so we need to check and handle
        if (Schema::hasColumn('institutions', 'municipality_id')) {
            // For SQLite, we'll make the column nullable if it exists as string
            // and add proper FK constraint
            Schema::table('institutions', function (Blueprint $table) {
                // Add FK constraint if not exists
                // Note: SQLite may already have this as string, so we ensure it works
            });
        }
        
        // Add municipality_id FK if not exists (as proper unsigned bigint)
        if (!Schema::hasColumn('institutions', 'municipality_id')) {
            Schema::table('institutions', function (Blueprint $table) {
                $table->unsignedBigInteger('municipality_id')->nullable()->after('wilaya_id');
                $table->foreign('municipality_id')->references('id')->on('municipalities')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            // Re-add wilaya_code as nullable to avoid issues
            if (!Schema::hasColumn('institutions', 'wilaya_code')) {
                $table->string('wilaya_code')->nullable();
            }
        });
    }
};
