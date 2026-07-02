<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->unsignedBigInteger('fin_year')->nullable();
            $table->foreign('fin_year')->references('id')->on('financial_years')->onDelete('set null');

            // Employee — linked to driver or standalone
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('set null');
            $table->string('employee_name');           // denormalised for standalone staff
            $table->string('employee_type')->default('driver'); // driver | staff

            // Period
            $table->date('payroll_month');             // stored as Y-m-01

            // Earnings
            $table->decimal('basic_salary',  12, 2)->default(0);
            $table->decimal('hra',           12, 2)->default(0);
            $table->decimal('da',            12, 2)->default(0);
            $table->decimal('other_allowance', 12, 2)->default(0);
            $table->decimal('bonus',         12, 2)->default(0);

            // Deductions
            $table->decimal('pf',            12, 2)->default(0);
            $table->decimal('esi',           12, 2)->default(0);
            $table->decimal('tds',           12, 2)->default(0);
            $table->decimal('advance_deduction', 12, 2)->default(0); // recovered advance
            $table->decimal('other_deduction',   12, 2)->default(0);

            // Computed
            $table->decimal('gross_salary',  12, 2)->default(0);
            $table->decimal('total_deductions', 12, 2)->default(0);
            $table->decimal('net_salary',    12, 2)->default(0);

            // Payment
            $table->string('payment_mode', 20)->default('cash'); // cash|upi|bank|cheque
            $table->string('reference_no', 100)->nullable();
            $table->date('payment_date')->nullable();
            $table->string('status', 20)->default('pending'); // pending|paid

            $table->text('notes')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
