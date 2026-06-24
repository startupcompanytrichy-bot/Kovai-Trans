<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->unsignedBigInteger('fin_year')->nullable();
            $table->foreign('fin_year')->references('id')->on('financial_years')->onDelete('set null');
            $table->foreignId('trip_id')->constrained('trips')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->string('payment_mode', 20)->nullable(); // cash,upi,bank,cheque
            $table->string('reference', 255)->nullable();   // UPI txn / cheque no / NEFT ref
            $table->text('note')->nullable();
            $table->date('paid_on')->default(DB::raw('CURRENT_DATE'));
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_payments');
    }
};
