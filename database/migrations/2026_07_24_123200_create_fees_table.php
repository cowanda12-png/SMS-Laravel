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
                $table->date('due_date');
                $table->date('paid_date')->nullable();
                $table->string('description')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('fees');
    }
};