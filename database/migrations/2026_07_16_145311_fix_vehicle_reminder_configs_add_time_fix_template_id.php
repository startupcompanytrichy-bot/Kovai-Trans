<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add missing `time` column if not already present
        if (!Schema::hasColumn('vehicle_reminder_configs', 'time')) {
            Schema::table('vehicle_reminder_configs', function (Blueprint $table) {
                $table->string('time', 20)->nullable()->after('duration');
            });
        }

        // 2. Drop existing FK on template_id if it exists (PostgreSQL safe check)
        $fkExists = DB::selectOne("
            SELECT constraint_name
            FROM information_schema.table_constraints
            WHERE table_name = 'vehicle_reminder_configs'
              AND constraint_name = 'vehicle_reminder_configs_template_id_foreign'
              AND constraint_type = 'FOREIGN KEY'
        ");

        if ($fkExists) {
            Schema::table('vehicle_reminder_configs', function (Blueprint $table) {
                $table->dropForeign(['template_id']);
            });
        }

        // 3. Change template_id from string/varchar to unsignedBigInteger
        // PostgreSQL USING cast to bigint
        DB::statement('ALTER TABLE vehicle_reminder_configs ALTER COLUMN template_id TYPE BIGINT USING template_id::BIGINT');

        // 4. Add FK to message_templates
        $newFkExists = DB::selectOne("
            SELECT constraint_name
            FROM information_schema.table_constraints
            WHERE table_name = 'vehicle_reminder_configs'
              AND constraint_name = 'vehicle_reminder_configs_template_id_foreign'
              AND constraint_type = 'FOREIGN KEY'
        ");

        if (!$newFkExists && Schema::hasTable('message_templates')) {
            Schema::table('vehicle_reminder_configs', function (Blueprint $table) {
                $table->foreign('template_id')
                      ->references('id')
                      ->on('message_templates')
                      ->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        // Drop FK if it exists
        $fkExists = DB::selectOne("
            SELECT constraint_name
            FROM information_schema.table_constraints
            WHERE table_name = 'vehicle_reminder_configs'
              AND constraint_name = 'vehicle_reminder_configs_template_id_foreign'
              AND constraint_type = 'FOREIGN KEY'
        ");

        if ($fkExists) {
            Schema::table('vehicle_reminder_configs', function (Blueprint $table) {
                $table->dropForeign(['template_id']);
            });
        }

        // Revert template_id to varchar
        DB::statement('ALTER TABLE vehicle_reminder_configs ALTER COLUMN template_id TYPE VARCHAR(255) USING template_id::VARCHAR');

        // Drop time column
        if (Schema::hasColumn('vehicle_reminder_configs', 'time')) {
            Schema::table('vehicle_reminder_configs', function (Blueprint $table) {
                $table->dropColumn('time');
            });
        }
    }
};
