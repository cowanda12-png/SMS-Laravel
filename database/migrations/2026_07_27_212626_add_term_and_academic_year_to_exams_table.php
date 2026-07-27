<?php
// database/migrations/2026_07_28_add_term_and_academic_year_to_exams_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            if (!Schema::hasColumn('exams', 'term')) {
                $table->string('term')->nullable()->after('status');
            }
            if (!Schema::hasColumn('exams', 'academic_year')) {
                $table->string('academic_year')->nullable()->after('term');
            }
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn(['term', 'academic_year']);
        });
    }
};