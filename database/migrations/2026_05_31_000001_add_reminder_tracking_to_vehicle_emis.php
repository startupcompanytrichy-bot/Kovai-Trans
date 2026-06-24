<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_emis', function (Blueprint $table) {
            // Track if reminder has been sent for current due date
            $table->boolean('reminder_sent')->default(false)->comment('Whether 5-day reminder has been sent');
            $table->timestamp('reminder_sent_at')->nullable()->comment('When the reminder was sent');
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_emis', function (Blueprint $table) {
            $table->dropColumn(['reminder_sent', 'reminder_sent_at']);
        });
    }
};
