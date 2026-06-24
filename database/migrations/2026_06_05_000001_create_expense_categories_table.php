<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->string('key')->unique();          // slug: fuel, my_custom
            $table->string('label');                  // display name
            $table->string('icon')->default('ti-more-alt');
            $table->string('color')->default('#8a94a6');
            $table->string('bg')->default('#f4f6fb');
            $table->boolean('is_custom')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        // Seed the default categories
        DB::table('expense_categories')->insert([
            ['key'=>'fuel',        'label'=>'Fuel',        'icon'=>'ti-dropbox',      'color'=>'#e53e3e','bg'=>'#fff5f5','is_custom'=>false,'is_active'=>true,'created_at'=>now(),'updated_at'=>now()],
            ['key'=>'toll',        'label'=>'Toll',         'icon'=>'ti-map',          'color'=>'#7c3aed','bg'=>'#f5f3ff','is_custom'=>false,'is_active'=>true,'created_at'=>now(),'updated_at'=>now()],
            ['key'=>'driver_bata', 'label'=>'Driver Bata',  'icon'=>'ti-user',         'color'=>'#38a169','bg'=>'#f0fff4','is_custom'=>false,'is_active'=>true,'created_at'=>now(),'updated_at'=>now()],
            ['key'=>'food',        'label'=>'Food',         'icon'=>'ti-cup',          'color'=>'#d97706','bg'=>'#fffbeb','is_custom'=>false,'is_active'=>true,'created_at'=>now(),'updated_at'=>now()],
            ['key'=>'repair',      'label'=>'Repair',       'icon'=>'ti-settings',     'color'=>'#0369a1','bg'=>'#f0f9ff','is_custom'=>false,'is_active'=>true,'created_at'=>now(),'updated_at'=>now()],
            ['key'=>'maintenance', 'label'=>'Maintenance',  'icon'=>'ti-wrench',       'color'=>'#b45309','bg'=>'#fff8e1','is_custom'=>false,'is_active'=>true,'created_at'=>now(),'updated_at'=>now()],
            ['key'=>'parking',     'label'=>'Parking',      'icon'=>'ti-location-pin', 'color'=>'#667eea','bg'=>'#eef2ff','is_custom'=>false,'is_active'=>true,'created_at'=>now(),'updated_at'=>now()],
            ['key'=>'other',       'label'=>'Other',        'icon'=>'ti-more-alt',     'color'=>'#8a94a6','bg'=>'#f4f6fb','is_custom'=>false,'is_active'=>true,'created_at'=>now(),'updated_at'=>now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_categories');
    }
};
