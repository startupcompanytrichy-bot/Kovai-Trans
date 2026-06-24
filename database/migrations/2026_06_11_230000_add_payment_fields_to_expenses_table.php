<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Credit payment tracking
            $table->decimal('paid_amount', 12, 2)->default(0)->after('amount');   // total collected so far
            $table->string('payment_status', 20)->default('unpaid')->after('paid_amount'); // unpaid | partial | paid
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['paid_amount', 'payment_status']);
        });
    }
};
