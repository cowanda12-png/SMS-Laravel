<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('mpesa_transactions')) {
            Schema::create('mpesa_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id'); // NO foreign key constraint here
                $table->string('transaction_code')->unique();
                $table->string('checkout_request_id')->nullable();
                $table->string('result_code')->nullable();
                $table->string('result_desc')->nullable();
                $table->decimal('amount', 10, 2);
                $table->string('phone_number')->nullable();
                $table->string('account_reference')->nullable();
                $table->string('mpesa_receipt_number')->nullable();
                $table->json('request_data')->nullable();
                $table->json('response_data')->nullable();
                $table->string('status')->default('pending');
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
                
                // Indexes
                $table->index('student_id');
                $table->index('transaction_code');
                $table->index('status');
                $table->index('account_reference');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('mpesa_transactions');
    }
};