<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            // Only add columns that don't exist
            if (! Schema::hasColumn('institutions', 'wilaya_id')) {
                $table->unsignedBigInteger('wilaya_id')->nullable()->after('id');
                $table->foreign('wilaya_id')->references('id')->on('wilayas')->nullOnDelete();
            }

            if (! Schema::hasColumn('institutions', 'name_ar')) {
                $table->string('name_ar')->nullable()->after('name');
            }
            if (! Schema::hasColumn('institutions', 'address')) {
                $table->string('address')->nullable();
            }
            if (! Schema::hasColumn('institutions', 'phone')) {
                $table->string('phone')->nullable();
            }
            if (! Schema::hasColumn('institutions', 'email')) {
                $table->string('email')->nullable();
            }
            if (! Schema::hasColumn('institutions', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
            if (! Schema::hasColumn('institutions', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $columns = ['wilaya_id', 'name_ar', 'address', 'phone', 'email', 'is_active', 'deleted_at'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('institutions', $column)) {
                    if ($column === 'wilaya_id') {
                        $table->dropForeign(['wilaya_id']);
                    }
                    $table->dropColumn($column);
                }
            }
        });
    }
};
