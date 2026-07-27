<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('students', 'alternate_phone')) {
                $table->string('alternate_phone')->nullable()->after('phone');
            }
            
            if (!Schema::hasColumn('students', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('address');
            }
            
            if (!Schema::hasColumn('students', 'gender')) {
                $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('date_of_birth');
            }
            
            if (!Schema::hasColumn('students', 'guardian_name')) {
                $table->string('guardian_name')->nullable()->after('gender');
            }
            
            if (!Schema::hasColumn('students', 'guardian_phone')) {
                $table->string('guardian_phone')->nullable()->after('guardian_name');
            }
            
            if (!Schema::hasColumn('students', 'guardian_email')) {
                $table->string('guardian_email')->nullable()->after('guardian_phone');
            }
            
            if (!Schema::hasColumn('students', 'grade_id')) {
                $table->foreignId('grade_id')->nullable()->constrained('grades')->after('class_id');
            }
            
            if (!Schema::hasColumn('students', 'enrollment_date')) {
                $table->date('enrollment_date')->nullable()->after('status');
            }
            
            if (!Schema::hasColumn('students', 'profile_image')) {
                $table->string('profile_image')->nullable()->after('enrollment_date');
            }
            
            if (!Schema::hasColumn('students', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }
        });
    }

    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $columns = [
                'alternate_phone',
                'date_of_birth',
                'gender',
                'guardian_name',
                'guardian_phone',
                'guardian_email',
                'grade_id',
                'enrollment_date',
                'profile_image',
                'deleted_at'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('students', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};