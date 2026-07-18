<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->updateOrInsert(
            ['key' => 'whatsapp_reminder_days'],
            [
                'value'      => '15',
                'group'      => 'whatsapp_config',
                'label'      => 'WhatsApp Reminder Days Before Expiry',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'whatsapp_reminder_days')->delete();
    }
};
