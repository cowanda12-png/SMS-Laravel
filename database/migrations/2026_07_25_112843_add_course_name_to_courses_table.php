<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCourseNameToCoursesTable extends Migration  // ← Note the naming convention
{
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->string('course_name')->after('id');
        });
    }

    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('course_name');
        });
    }
}