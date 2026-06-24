<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\GeneralApiController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\PartiesController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehicleEmiController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TraderController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\UserPermissionController;
use App\Http\Controllers\ExpenseLedgerController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckLogin;


Route::get('/', [AuthController::class, 'index'])->name('login');
Route::post('/Login', [AuthController::class, 'login'])->name('Login');

// ── Forgot Password (OTP-based) ────────────────────────────────────────────
Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.forgot');
Route::post('/forgot-password', [AuthController::class, 'sendOtp'])->name('password.send-otp');
Route::get('/verify-otp', [AuthController::class, 'verifyForm'])->name('password.verify-form');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('password.verify-otp');
Route::get('/reset-password', [AuthController::class, 'resetForm'])->name('password.reset-form');
Route::post('/reset-password', [AuthController::class, 'updatePassword'])->name('password.reset');

Route::get('/dashboard', [HomeController::class, 'index'])->middleware(CheckLogin::class)->name('dashboard');
Route::post('/logout', [HomeController::class, 'logout'])->name('logout');
Route::get('/company', [MasterController::class, 'company'])->name('company');
Route::get('/company/add', [MasterController::class, 'add'])->name('company.add');
Route::post('/company/add', [MasterController::class, 'store'])->name('company.store');
Route::get('/company/view/{id}', [MasterController::class, 'view'])->name('company.view');
Route::get('/company/edit/{id}', [MasterController::class, 'edit'])->name('company.edit');
Route::put('/company/{id}', [MasterController::class, 'update'])->name('company.update');
Route::delete('/company/{id}', [MasterController::class, 'destroy'])->name('company.destroy');
Route::get('/branch', [MasterController::class, 'branch'])->name('branch');
Route::get('/branch/add', [MasterController::class, 'branchAdd'])->name('branch.add');
Route::post('/branch/add', [MasterController::class, 'branchStore'])->name('branch.store');
Route::get('/branch/view/{id}', [MasterController::class, 'branchView'])->name('branch.view');
Route::get('/branch/edit/{id}', [MasterController::class, 'branchEdit'])->name('branch.edit');
Route::put('/branch/{id}', [MasterController::class, 'branchUpdate'])->name('branch.update');
Route::delete('/branch/{id}', [MasterController::class, 'branchDestroy'])->name('branch.destroy');
Route::get('/parties', [PartiesController::class, 'parties'])->name('parties');
Route::post('/parties', [PartiesController::class, 'store'])->name('parties.store');
Route::get('/parties/edit/{id}', [PartiesController::class, 'edit'])->name('parties.edit');
Route::get('/parties/view/{id}', [PartiesController::class, 'view'])->name('parties.view');
Route::put('/parties/{id}', [PartiesController::class, 'update'])->name('parties.update');
Route::delete('/parties/{id}', [PartiesController::class, 'destroy'])->name('parties.destroy');

Route::get('/vehicle', [VehicleController::class, 'index'])->name('vehicle');
Route::get('/vehicle/add', [VehicleController::class, 'add'])->name('vehicle.add');
Route::post('/vehicle', [VehicleController::class, 'store'])->name('vehicle.store');
Route::get('/vehicle/edit/{id}', [VehicleController::class, 'edit'])->name('vehicle.edit');
Route::get('/vehicle/view/{id}', [VehicleController::class, 'view'])->name('vehicle.view');
Route::get('/vehicle/data/{id}', [VehicleController::class, 'data'])->name('vehicle.data');
Route::put('/vehicle/{id}', [VehicleController::class, 'update'])->name('vehicle.update');
Route::delete('/vehicle/{id}', [VehicleController::class, 'destroy'])->name('vehicle.destroy');

Route::get('/supplier', [SupplierController::class, 'index'])->name('supplier');
Route::post('/supplier', [SupplierController::class, 'store'])->name('supplier.store');
Route::get('/supplier/edit/{id}', [SupplierController::class, 'edit'])->name('supplier.edit');
Route::get('/supplier/view/{id}', [SupplierController::class, 'view'])->name('supplier.view');
Route::put('/supplier/{id}', [SupplierController::class, 'update'])->name('supplier.update');
Route::delete('/supplier/{id}', [SupplierController::class, 'destroy'])->name('supplier.destroy');

Route::get('/trader', [TraderController::class, 'index'])->name('trader');
Route::post('/trader', [TraderController::class, 'store'])->name('trader.store');
Route::get('/trader/edit/{id}', [TraderController::class, 'edit'])->name('trader.edit');
Route::get('/trader/view/{id}', [TraderController::class, 'view'])->name('trader.view');
Route::put('/trader/{id}', [TraderController::class, 'update'])->name('trader.update');
Route::delete('/trader/{id}', [TraderController::class, 'destroy'])->name('trader.destroy');

Route::get('/driver', [DriverController::class, 'index'])->name('driver');
Route::get('/driver/add', [DriverController::class, 'create'])->name('driver.create');
Route::post('/driver', [DriverController::class, 'store'])->name('driver.store');
Route::get('/driver/edit/{id}', [DriverController::class, 'edit'])->name('driver.edit');
Route::get('/driver/view/{id}', [DriverController::class, 'view'])->name('driver.view');
Route::put('/driver/{id}', [DriverController::class, 'update'])->name('driver.update');
Route::delete('/driver/{id}', [DriverController::class, 'destroy'])->name('driver.destroy');

Route::get('/trip', [TripController::class, 'index'])->name('trip');
Route::get('/trip/add', [TripController::class, 'create'])->name('trip.create');
Route::post('/trip', [TripController::class, 'store'])->name('trip.store');
Route::get('/trip/edit/{id}', [TripController::class, 'edit'])->name('trip.edit');
Route::get('/trip/view/{id}', [TripController::class, 'view'])->name('trip.view');
Route::put('/trip/{id}', [TripController::class, 'update'])->name('trip.update');
Route::patch('/trip/{id}/status', [TripController::class, 'updateStatus'])->name('trip.status');
Route::put('/trip/{id}/payment', [TripController::class, 'updatePayment'])->name('trip.updatePayment');
Route::post('/trip/{id}/payments', [TripController::class, 'addPayment'])->name('trip.payment.add');
Route::delete('/trip/{id}/payments/{paymentId}', [TripController::class, 'deletePayment'])->name('trip.payment.delete');
Route::delete('/trip/{id}', [TripController::class, 'destroy'])->name('trip.destroy');
Route::get('/trip/generate-lr', [TripController::class, 'generateLrNo'])->name('trip.generate-lr');

// ── Expense Module ─────────────────────────────────────────────────────────
Route::post('/expense/category', [ExpenseController::class, 'storeCategory'])->name('expense.category.store');
Route::get('/expense', [ExpenseController::class, 'index'])->name('expense');
Route::get('/expense/add', [ExpenseController::class, 'create'])->name('expense.create');
Route::post('/expense', [ExpenseController::class, 'store'])->name('expense.store');

// Expense Ledger
Route::get('/expense/ledger', [ExpenseLedgerController::class, 'index'])->name('expense.ledger.index');
Route::get('/expense/ledger/{category}', [ExpenseLedgerController::class, 'showCategory'])->name('expense.ledger.category');
Route::get('/expense/ledger/{category}/pdf', [ExpenseLedgerController::class, 'pdf'])->name('expense.ledger.pdf');

Route::get('/expense/edit/{id}', [ExpenseController::class, 'edit'])->name('expense.edit');
Route::put('/expense/{id}', [ExpenseController::class, 'update'])->name('expense.update');
Route::post('/expense/{id}/approve', [ExpenseController::class, 'approve'])->name('expense.approve');
Route::post('/expense/{id}/reject', [ExpenseController::class, 'reject'])->name('expense.reject');
Route::delete('/expense/{id}', [ExpenseController::class, 'destroy'])->name('expense.destroy');
// Credit payment collection
Route::post('/expense/{id}/pay', [ExpenseController::class, 'collectPayment'])->name('expense.pay');
Route::get('/expense/{id}/payments', [ExpenseController::class, 'paymentHistory'])->name('expense.payments');

// ── Vehicle EMI Module ─────────────────────────────────────────────────────
Route::get('/emi', [VehicleEmiController::class, 'index'])->name('emi');
Route::get('/emi/add', [VehicleEmiController::class, 'create'])->name('emi.create');
Route::post('/emi', [VehicleEmiController::class, 'store'])->name('emi.store');
Route::get('/emi/edit/{id}', [VehicleEmiController::class, 'edit'])->name('emi.edit');
Route::put('/emi/{id}',  [VehicleEmiController::class, 'update'])->name('emi.update');
Route::delete('/emi/{id}', [VehicleEmiController::class, 'destroy'])->name('emi.destroy');
Route::post('/emi/{id}/pay', [VehicleEmiController::class, 'payStore'])->name('emi.pay');

// ── Reports Module ─────────────────────────────────────────────────────────
Route::get('/reports', [ReportController::class, 'index'])->name('reports');
Route::get('/reports/trips', [ReportController::class, 'trips'])->name('reports.trips');
Route::get('/reports/trips/pdf', [ReportController::class, 'tripsPdf'])->name('reports.trips.pdf');
Route::get('/reports/expenses', [ReportController::class, 'expenses'])->name('reports.expenses');
Route::get('/reports/pnl', [ReportController::class, 'pnl'])->name('reports.pnl');
Route::get('/reports/collection', [ReportController::class, 'collection'])->name('reports.collection');
Route::get('/reports/invoices', [ReportController::class, 'invoices'])->name('reports.invoices');
Route::get('/reports/invoices/pdf', [ReportController::class, 'invoicesPdf'])->name('reports.invoices.pdf');
Route::get('/reports/invoices/excel', [ReportController::class, 'invoicesExcel'])->name('reports.invoices.excel');
Route::post('/reports/invoices/print-selected', [ReportController::class, 'invoicesPrintSelected'])->name('reports.invoices.print');
Route::get('/reports/emi',  [ReportController::class, 'emi'])->name('reports.emi');
Route::get('/reports/emi/pdf', [ReportController::class, 'emiPdf'])->name('reports.emi.pdf');
Route::get('/reports/emi/excel', [ReportController::class, 'emiExcel'])->name('reports.emi.excel');
Route::get('/reports/parties-payment-ledger', [ReportController::class, 'partiesPaymentLedger'])->name('reports.parties-payment-ledger');
Route::get('/reports/parties-payment-ledger/pdf', [ReportController::class, 'partiesPaymentLedgerPdf'])->name('reports.parties-payment-ledger.pdf');
Route::get('/packing-slip/dashboard', [\App\Http\Controllers\PackingSlipController::class, 'dashboard'])->name('packing-slip.dashboard');
Route::get('/packing-slip', [\App\Http\Controllers\PackingSlipController::class, 'slipIndex'])->name('packing-slip.index');
Route::get('/packing-slip/create', [\App\Http\Controllers\PackingSlipController::class, 'createSlip'])->name('packing-slip.create');
Route::get('/packing-slip/{id}', [\App\Http\Controllers\PackingSlipController::class, 'showSlip'])->name('packing-slip.show');
Route::get('/packing-slip/{id}/edit', [\App\Http\Controllers\PackingSlipController::class, 'editSlip'])->name('packing-slip.edit');
Route::post('/packing-slip', [\App\Http\Controllers\PackingSlipController::class, 'storeSlip'])->name('packing-slip.store');
Route::get('/packing-slip/customers', [\App\Http\Controllers\PackingSlipController::class, 'customers'])->name('packing-slip.customers');
Route::post('/packing-slip/customers', [\App\Http\Controllers\PackingSlipController::class, 'storeCustomer'])->name('packing-slip.customers.store');
Route::get('/packing-slip/customers/edit/{id}', [\App\Http\Controllers\PackingSlipController::class, 'editCustomer'])->name('packing-slip.customers.edit');
Route::post('/packing-slip/customers/update/{id}', [\App\Http\Controllers\PackingSlipController::class, 'updateCustomer'])->name('packing-slip.customers.update');
Route::post('/packing-slip/customers/delete/{id}', [\App\Http\Controllers\PackingSlipController::class, 'destroyCustomer'])->name('packing-slip.customers.delete');
Route::get('/reports/packing-slip-ledger', [\App\Http\Controllers\PackingSlipController::class, 'index'])->name('reports.packing-slip-ledger');
Route::get('/packing-slip/qualities', [\App\Http\Controllers\PackingSlipController::class, 'qualities'])->name('packing-slip.qualities');
Route::post('/packing-slip/quality', [\App\Http\Controllers\PackingSlipController::class, 'storeQuality'])->name('packing-slip.quality.store');
Route::get('/packing-slip/quality/edit/{id}', [\App\Http\Controllers\PackingSlipController::class, 'editQuality'])->name('packing-slip.quality.edit');
Route::post('/packing-slip/quality/update/{id}', [\App\Http\Controllers\PackingSlipController::class, 'updateQuality'])->name('packing-slip.quality.update');
Route::post('/packing-slip/quality/delete/{id}', [\App\Http\Controllers\PackingSlipController::class, 'destroyQuality'])->name('packing-slip.quality.delete');
Route::get('/packing-slip/next-bale-no', [\App\Http\Controllers\PackingSlipController::class, 'getNextBaleNo'])->name('packing-slip.next-bale-no');
Route::get('/packing-slip/{id}/print', [\App\Http\Controllers\PackingSlipController::class, 'printSlip'])->name('packing-slip.print');

// ── Invoice Module ─────────────────────────────────────────────────────────
Route::get('/invoice', [InvoiceController::class, 'index'])->name('invoice.index');
Route::get('/invoice/view/{invoiceNo}', [InvoiceController::class, 'viewInvoice'])->name('invoice.view');
Route::get('/invoice/trip/{id}', [InvoiceController::class, 'trip'])->name('invoice.trip');
Route::get('/invoice/print/{id}', [InvoiceController::class, 'print'])->name('invoice.print');
Route::post('/invoice/multi', [InvoiceController::class, 'multi'])->name('invoice.multi');
Route::post('/invoice/generate', [InvoiceController::class, 'generate'])->name('invoice.generate');
Route::post('/invoice/excel', [InvoiceController::class, 'exportExcel'])->name('invoice.excel');
Route::post('/invoice/pdf', [InvoiceController::class, 'exportPdf'])->name('invoice.pdf');
Route::post('/invoice/{invoiceNo}/payment', [InvoiceController::class, 'updatePayment'])->name('invoice.payment.update');

Route::get('/api/general/states', [GeneralApiController::class, 'getStates'])->name('api.general.states');
Route::get('/api/general/districts', [GeneralApiController::class, 'getDistricts'])->name('api.general.districts');
Route::get('/api/general/cities', [GeneralApiController::class, 'getCities'])->name('api.general.cities');
Route::get('/api/general/distance', [GeneralApiController::class, 'getDistance'])->name('api.general.distance');

// ── Settings ───────────────────────────────────────────────────────────────
Route::middleware(CheckLogin::class)->group(function () {
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/financial-year', [SettingsController::class, 'storeFY'])->name('settings.fy.store');
    Route::post('/settings/financial-year/{id}/set-default', [SettingsController::class, 'setDefaultFY'])->name('settings.fy.setDefault');
    Route::delete('/settings/financial-year/{id}', [SettingsController::class, 'destroyFY'])->name('settings.fy.destroy');
    Route::post('/settings/branch', [SettingsController::class, 'updateBranchSettings'])->name('settings.branch.update');
    Route::post('/settings/limits', [SettingsController::class, 'updateLimitSettings'])->name('settings.limits.update');
    Route::post('/settings/update', [SettingsController::class, 'updateSetting'])->name('settings.update');
    Route::get('/settings/permissions', [SettingsController::class, 'permissionIndex'])->name('settings.permissions.index');
    Route::get('/settings/permissions/{id}/edit', [SettingsController::class, 'editPermissions'])->name('settings.permissions.edit');
    Route::post('/settings/permissions/{id}', [SettingsController::class, 'updatePermissions'])->name('settings.permissions.update');
});

// ── User Permission Management (Super Admin Only) ────────────────────────────
Route::middleware(CheckLogin::class)->group(function () {
    Route::get('/user-permissions', [UserPermissionController::class, 'index'])->name('user-permissions.index');
    Route::get('/user-permissions/create', [UserPermissionController::class, 'create'])->name('user-permissions.create');
    Route::post('/user-permissions', [UserPermissionController::class, 'store'])->name('user-permissions.store');
    Route::get('/user-permissions/authorization', [UserPermissionController::class, 'authorization'])->name('user-permissions.authorization');
    Route::get('/user-permissions/{id}/authorize', [UserPermissionController::class, 'authorizeUser'])->name('user-permissions.authorize');
    Route::get('/user-permissions/{id}/edit', [UserPermissionController::class, 'edit'])->name('user-permissions.edit');
    Route::put('/user-permissions/{id}', [UserPermissionController::class, 'update'])->name('user-permissions.update');
    Route::delete('/user-permissions/{id}', [UserPermissionController::class, 'destroy'])->name('user-permissions.destroy');
    Route::post('/user-permissions/{id}/toggle-status', [UserPermissionController::class, 'toggleStatus'])->name('user-permissions.toggle-status');
});
