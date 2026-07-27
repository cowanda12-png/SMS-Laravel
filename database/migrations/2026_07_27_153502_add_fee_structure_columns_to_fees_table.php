<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fees', function (Blueprint $table) {
            // Add columns if they don't exist
            if (!Schema::hasColumn('fees', 'fee_structure_id')) {
                $table->unsignedBigInteger('fee_structure_id')->nullable()->after('student_id');
            }
            
            if (!Schema::hasColumn('fees', 'amount_paid')) {
                $table->decimal('amount_paid', 12, 2)->default(0)->after('amount');
            }
            
            if (!Schema::hasColumn('fees', 'balance')) {
                $table->decimal('balance', 12, 2)->default(0)->after('amount_paid');
            }
            
            if (!Schema::hasColumn('fees', 'class_id')) {
                $table->unsignedBigInteger('class_id')->nullable()->after('student_id');
            }
            
            if (!Schema::hasColumn('fees', 'grade_id')) {
                $table->unsignedBigInteger('grade_id')->nullable()->after('class_id');
            }
            
            if (!Schema::hasColumn('fees', 'notes')) {
                $table->text('notes')->nullable()->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('fees', function (Blueprint $table) {
            $columns = ['fee_structure_id', 'amount_paid', 'balance', 'class_id', 'grade_id', 'notes'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('fees', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};