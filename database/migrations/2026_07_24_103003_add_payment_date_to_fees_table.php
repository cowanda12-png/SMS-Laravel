<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('fees') && !Schema::hasColumn('fees', 'payment_date')) {
            Schema::table('fees', function (Blueprint $table) {
                $table->date('payment_date')->nullable()->after('amount');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('fees') && Schema::hasColumn('fees', 'payment_date')) {
            Schema::table('fees', function (Blueprint $table) {
                $table->dropColumn('payment_date');
            });
        }
    }
};