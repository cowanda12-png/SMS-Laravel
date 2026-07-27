<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('fee_id')->nullable()->constrained('fees')->onDelete('set null');
            $table->string('payment_reference')->unique();
            $table->string('payment_method')->nullable(); // cash, mpesa, bank, etc.
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('KES');
            $table->string('status')->default('pending'); // pending, paid, failed, refunded
            $table->date('payment_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('transaction_id')->nullable();
            $table->text('notes')->nullable();
            $table->string('receipt_number')->nullable();
            $table->json('payment_data')->nullable(); // For additional payment data
            $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['student_id', 'status']);
            $table->index(['payment_date', 'status']);
            $table->index('payment_reference');
            $table->index('transaction_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};