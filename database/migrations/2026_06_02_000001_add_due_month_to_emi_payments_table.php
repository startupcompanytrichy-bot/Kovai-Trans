<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('emi_payments', function (Blueprint $table) {
            $table->date('due_month')->nullable()->after('vehicle_emi_id')
                  ->comment('The month (Y-m-01) this payment is for');
        });
    }

    public function down(): void
    {
        Schema::table('emi_payments', function (Blueprint $table) {
            $table->dropColumn('due_month');
        });
    }
};
