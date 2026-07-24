<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Only add foreign keys if both tables exist
        if (Schema::hasTable('students')) {
            
            // Add foreign key to fees table
            if (Schema::hasTable('fees')) {
                try {
                    Schema::table('fees', function (Blueprint $table) {
                        // Check if foreign key doesn't already exist
                        $foreignKeys = $this->getForeignKeys('fees');
                        if (!in_array('fees_student_id_foreign', $foreignKeys)) {
                            $table->foreign('student_id')
                                  ->references('id')
                                  ->on('students')
                                  ->onDelete('cascade');
                        }
                    });
                } catch (\Exception $e) {
                    // Foreign key might already exist or table doesn't have column
                }
            }
            
            // Add foreign key to mpesa_transactions table
            if (Schema::hasTable('mpesa_transactions')) {
                try {
                    Schema::table('mpesa_transactions', function (Blueprint $table) {
                        $foreignKeys = $this->getForeignKeys('mpesa_transactions');
                        if (!in_array('mpesa_transactions_student_id_foreign', $foreignKeys)) {
                            $table->foreign('student_id')
                                  ->references('id')
                                  ->on('students')
                                  ->onDelete('cascade');
                        }
                    });
                } catch (\Exception $e) {
                    // Foreign key might already exist or table doesn't have column
                }
            }
        }
    }

    public function down(): void
    {
        // Remove foreign keys
        if (Schema::hasTable('fees')) {
            try {
                Schema::table('fees', function (Blueprint $table) {
                    $table->dropForeign(['student_id']);
                });
            } catch (\Exception $e) {
                // Foreign key might not exist
            }
        }
        
        if (Schema::hasTable('mpesa_transactions')) {
            try {
                Schema::table('mpesa_transactions', function (Blueprint $table) {
                    $table->dropForeign(['student_id']);
                });
            } catch (\Exception $e) {
                // Foreign key might not exist
            }
        }
    }
    
    private function getForeignKeys($table)
    {
        $conn = Schema::getConnection();
        $database = $conn->getDatabaseName();
        
        // For PostgreSQL
        if ($conn->getDriverName() === 'pgsql') {
            $schema = $conn->getConfig('schema') ?: 'public';
            $result = $conn->select("
                SELECT conname
                FROM pg_constraint
                JOIN pg_class ON pg_constraint.conrelid = pg_class.oid
                JOIN pg_namespace ON pg_class.relnamespace = pg_namespace.oid
                WHERE pg_class.relname = ?
                AND pg_namespace.nspname = ?
                AND pg_constraint.contype = 'f'
            ", [$table, $schema]);
            
            return array_map(function ($row) {
                return $row->conname;
            }, $result);
        }
        
        // For MySQL
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