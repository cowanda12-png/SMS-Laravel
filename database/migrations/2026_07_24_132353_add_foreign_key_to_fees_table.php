<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('fees') && Schema::hasTable('students')) {
            Schema::table('fees', function (Blueprint $table) {
                // Check if the foreign key doesn't already exist
                $foreignKeys = $this->getForeignKeys('fees');
                if (!in_array('fees_student_id_foreign', $foreignKeys)) {
                    $table->foreign('student_id')
                          ->references('id')
                          ->on('students')
                          ->onDelete('cascade');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('fees')) {
            Schema::table('fees', function (Blueprint $table) {
                $table->dropForeign(['student_id']);
            });
        }
    }
    
    // Helper method to check existing foreign keys (MySQL compatible)
    private function getForeignKeys($table)
    {
        $conn = Schema::getConnection();
        $database = $conn->getDatabaseName();
        
        $result = $conn->select("
            SELECT CONSTRAINT_NAME 
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = ? 
            AND TABLE_NAME = ?
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ", [$database, $table]);
        
        return array_map(function ($row) {
            return $row->CONSTRAINT_NAME;
        }, $result);
    }
};