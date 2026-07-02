<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->string('alternative_mobile', 20)->nullable()->after('mobile');
            $table->unsignedBigInteger('bank_name_id')->nullable()->after('pan_number');
            $table->foreign('bank_name_id')->references('id')->on('banks')->onDelete('set null');
            $table->string('account_number', 30)->nullable()->after('bank_name_id');
            $table->string('ifsc_code', 15)->nullable()->after('account_number');
            $table->string('branch_name', 100)->nullable()->after('ifsc_code');
        });
    }

    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropForeign(['bank_name_id']);
            $table->dropColumn(['alternative_mobile', 'bank_name_id', 'account_number', 'ifsc_code', 'branch_name']);
        });
    }
};
