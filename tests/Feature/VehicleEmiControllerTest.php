<?php

namespace Tests\Feature;

use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleEmiControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_defaults_outstanding_balance_to_loan_amount_when_not_provided(): void
    {
        $vehicle = Vehicle::create([
            'vehicle_number' => 'TN-01-1234',
            'vehicle_name' => 'Truck-1',
            'vehicle_type' => 'Truck',
            'status' => 'active',
        ]);

        $response = $this->post('/emi', [
            'vehicle_id' => $vehicle->id,
            'financier_name' => 'Bajaj Finance',
            'loan_amount' => '1700000.00',
            'emi_amount' => '36000.00',
            'interest_rate' => '7.00',
            'loan_start_date' => '2026-05-29',
            'loan_end_date' => '2030-12-30',
            'total_emis' => '50',
            'next_due_date' => '2026-05-01',
            'status' => 'active',
        ]);

        $response->assertRedirect('/emi');

        $this->assertDatabaseHas('vehicle_emis', [
            'vehicle_id' => $vehicle->id,
            'financier_name' => 'Bajaj Finance',
            'loan_amount' => 1700000.00,
            'emi_amount' => 36000.00,
            'outstanding_balance' => 1700000.00,
        ]);
    }
}
