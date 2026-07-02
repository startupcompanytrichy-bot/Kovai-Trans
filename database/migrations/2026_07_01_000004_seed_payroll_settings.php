<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $settings = [
        ['key' => 'pf_percentage',        'value' => '12',  'group' => 'payroll', 'label' => 'PF Percentage (%)'],
        ['key' => 'esi_percentage',        'value' => '0.75','group' => 'payroll', 'label' => 'ESI Percentage (%)'],
        ['key' => 'pf_employer_percentage','value' => '13',  'group' => 'payroll', 'label' => 'PF Employer Percentage (%)'],
        ['key' => 'esi_employer_percentage','value' => '3.25','group' => 'payroll', 'label' => 'ESI Employer Percentage (%)'],
        ['key' => 'pf_wage_ceiling',       'value' => '15000','group' => 'payroll', 'label' => 'PF Wage Ceiling (₹)'],
        ['key' => 'esi_wage_ceiling',      'value' => '21000','group' => 'payroll', 'label' => 'ESI Wage Ceiling (₹)'],
        ['key' => 'tds_default',           'value' => '0',   'group' => 'payroll', 'label' => 'TDS Default Amount (₹)'],
    ];

    public function up(): void
    {
        foreach ($this->settings as $s) {
            $exists = DB::table('settings')->where('key', $s['key'])->exists();
            if (!$exists) {
                DB::table('settings')->insert(array_merge($s, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', array_column($this->settings, 'key'))->delete();
    }
};
