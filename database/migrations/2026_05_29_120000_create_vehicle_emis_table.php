<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_emis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->unsignedBigInteger('fin_year')->nullable();
            $table->foreign('fin_year')->references('id')->on('financial_years')->onDelete('set null');
            $table->unsignedBigInteger('vehicle_id');
            $table->string('financier_name');
            $table->decimal('loan_amount', 14, 2)->default(0);
            $table->decimal('emi_amount', 12, 2)->default(0);
            $table->decimal('interest_rate', 6, 2)->nullable();
            $table->date('loan_start_date');
            $table->date('loan_end_date')->nullable();
            $table->integer('total_emis')->nullable();
            $table->integer('paid_emis')->default(0);
            $table->date('next_due_date')->nullable();
            $table->decimal('outstanding_balance', 14, 2)->default(0);
            $table->string('status')->default('active'); // active, closed, overdue
            $table->text('notes')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::create('emi_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->unsignedBigInteger('fin_year')->nullable();
            $table->foreign('fin_year')->references('id')->on('financial_years')->onDelete('set null');
            $table->unsignedBigInteger('vehicle_emi_id');
            $table->date('payment_date');
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->decimal('penalty', 12, 2)->default(0);
            $table->string('payment_mode')->nullable(); // cash, upi, bank, cheque
            $table->string('reference_no')->nullable();
            $table->string('receipt_image')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emi_payments');
        Schema::dropIfExists('vehicle_emis');
    }
};
