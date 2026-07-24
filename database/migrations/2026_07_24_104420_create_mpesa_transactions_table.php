<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mpesa_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_no')->unique();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('phone_number');
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['Completed', 'Pending', 'Failed'])->default('Pending');
            $table->string('mpesa_receipt')->nullable();
            $table->timestamp('transaction_date')->nullable();
            
            // Optional fields for M-Pesa API integration
            $table->string('checkout_request_id')->nullable()->index();
            $table->string('merchant_request_id')->nullable()->index();
            $table->string('result_code')->nullable();
            $table->text('result_desc')->nullable();
            
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['status', 'transaction_date']);
            $table->index(['student_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mpesa_transactions');
    }
};