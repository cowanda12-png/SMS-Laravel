<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('fees')) {
            Schema::create('fees', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained()->onDelete('cascade');
                $table->decimal('amount', 10, 2);
                $table->date('payment_date')->nullable();
                $table->date('due_date');
                $table->string('status')->default('unpaid');
                $table->string('term')->nullable();
                $table->string('academic_year')->nullable();
                $table->string('payment_method')->nullable();
                $table->string('fee_type')->nullable();
                $table->text('description')->nullable();
                $table->string('receipt_no')->nullable();
                $table->timestamp('paid_at')->nullable();
                
                // M-Pesa fields
                $table->string('mpesa_phone')->nullable();
                $table->string('mpesa_transaction_code')->nullable()->unique();
                $table->string('mpesa_checkout_request_id')->nullable();
                $table->string('mpesa_result_code')->nullable();
                $table->json('mpesa_response')->nullable();
                $table->string('account_reference')->nullable();
                $table->string('mpesa_result_desc')->nullable();
                $table->timestamp('completed_at')->nullable();
                
                $table->timestamps();
                
                // Add indexes for better performance
                $table->index('student_id');
                $table->index('status');
                $table->index('due_date');
                $table->index('mpesa_transaction_code');
                $table->index('account_reference');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('fees');
    }
};