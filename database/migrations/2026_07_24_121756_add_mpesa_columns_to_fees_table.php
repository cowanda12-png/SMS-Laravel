<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('fees')) {
            Schema::table('fees', function (Blueprint $table) {
                $columns = [
                    'mpesa_phone' => 'string',
                    'mpesa_transaction_code' => 'string',
                    'mpesa_checkout_request_id' => 'string',
                    'mpesa_result_code' => 'string',
                    'mpesa_response' => 'json',
                    'account_reference' => 'string',
                    'mpesa_result_desc' => 'string',
                    'completed_at' => 'timestamp'
                ];
                
                foreach ($columns as $column => $type) {
                    if (!Schema::hasColumn('fees', $column)) {
                        if ($type === 'timestamp') {
                            $table->timestamp($column)->nullable();
                        } elseif ($type === 'json') {
                            $table->json($column)->nullable();
                        } else {
                            $table->$type($column)->nullable();
                        }
                    }
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('fees')) {
            $columns = [
                'mpesa_phone',
                'mpesa_transaction_code',
                'mpesa_checkout_request_id',
                'mpesa_result_code',
                'mpesa_response',
                'account_reference',
                'mpesa_result_desc',
                'completed_at'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('fees', $column)) {
                    Schema::table('fees', function (Blueprint $table) use ($column) {
                        $table->dropColumn($column);
                    });
                }
            }
        }
    }
};