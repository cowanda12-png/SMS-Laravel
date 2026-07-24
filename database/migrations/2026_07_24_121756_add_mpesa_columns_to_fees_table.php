<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fees', function (Blueprint $table) {
            // Payment method column
            $table->enum('payment_method', ['Cash', 'Bank Transfer', 'Cheque', 'M-Pesa', 'Credit Card', 'Other'])
                  ->nullable()
                  ->after('amount');
            
            // M-Pesa specific columns
            $table->string('mpesa_phone', 20)->nullable()->after('payment_method');
            $table->string('mpesa_checkout_request_id', 100)->nullable()->after('mpesa_phone');
            $table->string('mpesa_transaction_code', 50)->nullable()->after('mpesa_checkout_request_id');
            $table->string('mpesa_result_code', 10)->nullable()->after('mpesa_transaction_code');
            $table->text('mpesa_result_desc')->nullable()->after('mpesa_result_code');
            $table->json('mpesa_response')->nullable()->after('mpesa_result_desc');
            
            // Fee type and description
            $table->string('fee_type', 50)->nullable()->after('mpesa_response');
            $table->text('description')->nullable()->after('fee_type');
            
            // Receipt number
            $table->string('receipt_no', 50)->nullable()->after('description');
            
            // Paid at timestamp
            $table->timestamp('paid_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('fees', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method',
                'mpesa_phone',
                'mpesa_checkout_request_id',
                'mpesa_transaction_code',
                'mpesa_result_code',
                'mpesa_result_desc',
                'mpesa_response',
                'fee_type',
                'description',
                'receipt_no',
                'paid_at'
            ]);
        });
    }
};