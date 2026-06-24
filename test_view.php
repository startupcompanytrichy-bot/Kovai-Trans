<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\Trip;
use App\Models\Vehicle;
use App\Models\Party;
use App\Models\Driver;
use App\Models\Supplier;

$trip = Trip::find(2);
if (!$trip) {
    echo "Trip ID 2 not found, using first()\n";
    $trip = Trip::first();
}
if (!$trip) {
    echo "No trip found in DB\n";
    exit;
}
try {
    $html = view('Trips.Edit_Trip', [
        'trip' => $trip,
        'vehicles' => Vehicle::all(),
        'parties' => Party::all(),
        'drivers' => Driver::all(),
        'suppliers' => Supplier::all(),
    ])->render();
    echo "Rendered successfully!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo $e->getTraceAsString() . "\n";
}
