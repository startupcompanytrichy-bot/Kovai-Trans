<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->string('invoice_no', 60)->nullable()->after('remarks');
            $table->string('invoice_type', 20)->nullable()->after('invoice_no'); // normal | rcm | exempt
            $table->string('invoice_status', 20)->nullable()->default('not_invoiced')->after('invoice_type'); // not_invoiced | invoiced
            $table->timestamp('invoiced_at')->nullable()->after('invoice_status');
        });
    }

    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn(['invoice_no', 'invoice_type', 'invoice_status', 'invoiced_at']);
        });
    }
};
