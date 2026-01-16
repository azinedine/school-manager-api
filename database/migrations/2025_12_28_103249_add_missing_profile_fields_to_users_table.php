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
            $table->string('name_ar')->nullable()->after('name');
            $table->enum('gender', ['male', 'female'])->nullable()->after('name_ar');
            $table->date('date_of_birth')->nullable()->after('gender');
            $table->string('address', 500)->nullable()->after('date_of_birth');
            $table->string('phone', 20)->nullable()->after('address');
            $table->integer('years_of_experience')->nullable()->after('institution_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'name_ar',
                'gender',
                'date_of_birth',
                'address',
                'phone',
                'years_of_experience',
            ]);
        });
    }
};
