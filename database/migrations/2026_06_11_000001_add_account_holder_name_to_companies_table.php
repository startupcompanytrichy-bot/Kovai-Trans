<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('companies', 'account_holder_name')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->string('account_holder_name')->nullable()->after('account_number');
            });
        }
        // Ensure phone columns also exist (in case billing migration wasn't run)
        if (!Schema::hasColumn('companies', 'phone')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->string('phone')->nullable()->after('email');
                $table->string('phone2')->nullable()->after('phone');
            });
        }
        if (!Schema::hasColumn('companies', 'bank_name')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->string('bank_name')->nullable();
                $table->string('account_number')->nullable();
                $table->string('ifsc_code')->nullable();
                $table->string('branch_name')->nullable();
                $table->string('upi_id')->nullable();
                $table->string('place_of_supply')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('account_holder_name');
        });
    }
};
