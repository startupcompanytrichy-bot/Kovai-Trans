<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'fin_year')) {
                $table->unsignedBigInteger('fin_year')->nullable()->after('id');
                $table->foreign('fin_year')->references('id')->on('financial_years')->onDelete('set null');
            }
            if (!Schema::hasColumn('companies', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('fin_year');
                $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
            }
            if (!Schema::hasColumn('companies', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            if (!Schema::hasColumn('companies', 'phone2')) {
                $table->string('phone2')->nullable()->after('phone');
            }
            $table->string('bank_name')->nullable()->after('gst');
            $table->string('account_number')->nullable()->after('bank_name');
            $table->string('ifsc_code')->nullable()->after('account_number');
            $table->string('branch_name')->nullable()->after('ifsc_code');
            $table->string('upi_id')->nullable()->after('branch_name');
            $table->string('place_of_supply')->nullable()->after('upi_id');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropForeign(['fin_year']);
            $table->dropForeign(['branch_id']);
            $table->dropColumn(['fin_year', 'branch_id']);
        });
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['phone','phone2','bank_name','account_number','ifsc_code','branch_name','upi_id','place_of_supply']);
        });
    }
};
