<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mpesa_transactions', function (Blueprint $table) {
            // Check if columns exist before adding
            if (!Schema::hasColumn('mpesa_transactions', 'transaction_date')) {
                $table->timestamp('transaction_date')->nullable();
            }
            if (!Schema::hasColumn('mpesa_transactions', 'checkout_request_id')) {
                $table->string('checkout_request_id')->nullable();
            }
            if (!Schema::hasColumn('mpesa_transactions', 'merchant_request_id')) {
                $table->string('merchant_request_id')->nullable();
            }
            if (!Schema::hasColumn('mpesa_transactions', 'result_code')) {
                $table->string('result_code')->nullable();
            }
            if (!Schema::hasColumn('mpesa_transactions', 'result_desc')) {
                $table->text('result_desc')->nullable();
            }
            
            // COMMENT OUT OR REMOVE THESE LINES
            // $table->index(['status', 'transaction_date']);
            // $table->index(['student_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('mpesa_transactions', function (Blueprint $table) {
            $columns = ['transaction_date', 'checkout_request_id', 'merchant_request_id', 'result_code', 'result_desc'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('mpesa_transactions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};