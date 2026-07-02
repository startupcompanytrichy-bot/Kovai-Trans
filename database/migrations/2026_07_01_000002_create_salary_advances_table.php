<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_advances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->unsignedBigInteger('fin_year')->nullable();
            $table->foreign('fin_year')->references('id')->on('financial_years')->onDelete('set null');

            $table->unsignedBigInteger('driver_id')->nullable();
            $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('set null');
            $table->string('employee_name');

            $table->decimal('amount', 12, 2);
            $table->date('advance_date');
            $table->string('payment_mode', 20)->default('cash');
            $table->string('reference_no', 100)->nullable();
            $table->decimal('recovered_amount', 12, 2)->default(0); // total recovered so far
            $table->string('status', 20)->default('pending');       // pending|partial|recovered
            $table->text('notes')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_advances');
    }
};
