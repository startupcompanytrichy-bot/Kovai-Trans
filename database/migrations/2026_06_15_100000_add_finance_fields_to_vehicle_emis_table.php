<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_emis', function (Blueprint $table) {
            $table->string('contract_no')->nullable()->after('financier_name');
            $table->decimal('interest_amount', 14, 2)->nullable()->after('loan_amount');
            $table->decimal('insurance_amount', 14, 2)->nullable()->after('interest_amount');
            $table->decimal('total_payable', 14, 2)->nullable()->after('insurance_amount');
            $table->date('agreement_date')->nullable()->after('loan_start_date');
            $table->date('first_instalment_date')->nullable()->after('agreement_date');
            $table->date('last_instalment_date')->nullable()->after('first_instalment_date');
            $table->string('asset_make')->nullable()->after('notes');
            $table->string('asset_type')->nullable()->after('asset_make');
        });

        Schema::table('emi_payments', function (Blueprint $table) {
            $table->string('particulars')->nullable()->after('notes');
            $table->string('dr_cr')->default('CR')->after('particulars'); // DR or CR
            $table->decimal('others_amount', 12, 2)->default(0)->after('dr_cr');
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_emis', function (Blueprint $table) {
            $table->dropColumn([
                'contract_no', 'interest_amount', 'insurance_amount', 'total_payable',
                'agreement_date', 'first_instalment_date', 'last_instalment_date',
                'asset_make', 'asset_type',
            ]);
        });

        Schema::table('emi_payments', function (Blueprint $table) {
            $table->dropColumn(['particulars', 'dr_cr', 'others_amount']);
        });
    }
};
