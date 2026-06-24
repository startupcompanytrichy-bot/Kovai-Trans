<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create traders table
        Schema::create('traders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->unsignedBigInteger('fin_year')->nullable();
            $table->foreign('fin_year')->references('id')->on('financial_years')->onDelete('set null');
            $table->string('name');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_deleted')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        // 2. Add trader_id to expenses table
        Schema::table('expenses', function (Blueprint $table) {
            $table->unsignedBigInteger('trader_id')->nullable()->after('driver_id');
            $table->foreign('trader_id')->references('id')->on('traders')->onDelete('set null');
        });

        // 3. Create expense_accessories table
        Schema::create('expense_accessories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->unsignedBigInteger('fin_year')->nullable();
            $table->foreign('fin_year')->references('id')->on('financial_years')->onDelete('set null');
            $table->unsignedBigInteger('expense_id');
            $table->string('accessory_name');
            $table->integer('quantity')->default(1);
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('amount', 12, 2)->default(0);
            $table->timestamps();

            $table->foreign('expense_id')->references('id')->on('expenses')->onDelete('cascade');
        });

        // 4. Seed the Accessories expense category
        if (!DB::table('expense_categories')->where('key', 'accessories')->exists()) {
            DB::table('expense_categories')->insert([
                'key'        => 'accessories',
                'label'      => 'Accessories',
                'icon'       => 'ti-package',
                'color'      => '#0ea5e9',
                'bg'         => '#f0f9ff',
                'is_custom'  => false,
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_accessories');

        if (Schema::hasColumn('expenses', 'trader_id')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->dropForeign(['trader_id']);
                $table->dropColumn('trader_id');
            });
        }

        Schema::dropIfExists('traders');

        DB::table('expense_categories')->where('key', 'accessories')->delete();
    }
};
