<?php

namespace Tests\Feature;

use App\Models\Trip;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_pnl_report_includes_total_trips_in_summary(): void
    {
        Trip::create([
            'trip_no' => 'TRIP-001',
            'trip_date' => '2026-05-01',
            'from_location' => 'A',
            'to_location' => 'B',
            'freight_amount' => 1000,
            'status' => 'completed',
            'is_deleted' => false,
        ]);

        Trip::create([
            'trip_no' => 'TRIP-002',
            'trip_date' => '2026-05-02',
            'from_location' => 'C',
            'to_location' => 'D',
            'freight_amount' => 2000,
            'status' => 'completed',
            'is_deleted' => false,
        ]);

        $response = $this->get('/reports/pnl');

        $response->assertOk()
            ->assertViewHas('summary', function ($summary) {
                return is_array($summary)
                    && $summary['total_trips'] === 2;
            });
    }

    public function test_trip_report_renders_without_blade_component_parse_errors(): void
    {
        $response = $this->get('/reports/trips');

        $response->assertOk();
    }
}
