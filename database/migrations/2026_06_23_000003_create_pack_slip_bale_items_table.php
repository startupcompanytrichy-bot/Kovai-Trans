<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pack_slip_bale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pack_slip_id')->constrained()->onDelete('cascade');
            $table->integer('bale_no');
            $table->integer('s_no');
            $table->decimal('meter', 12, 2)->nullable();
            $table->decimal('weight', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pack_slip_bale_items');
    }
};
