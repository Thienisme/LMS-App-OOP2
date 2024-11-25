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
        Schema::table('tbl_user', function (Blueprint $table) {
            $table->string('class', 50)->nullable()->after('is_admin');
            $table->string('student_code', 20)->nullable()->unique()->after('class');
            $table->string('phone', 15)->nullable()->after('student_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_user', function (Blueprint $table) {
            $table->dropColumn(['class', 'student_code', 'phone']);
        });
    }
};