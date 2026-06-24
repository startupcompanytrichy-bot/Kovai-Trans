<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Define permissions by module
        $permissionsData = [
            'dashboard' => [
                ['name' => 'view_dashboard', 'display_name' => 'View Dashboard'],
            ],
            'trip' => [
                ['name' => 'view_trips', 'display_name' => 'View Trips'],
                ['name' => 'create_trip', 'display_name' => 'Create Trip'],
                ['name' => 'edit_trip', 'display_name' => 'Edit Trip'],
                ['name' => 'delete_trip', 'display_name' => 'Delete Trip'],
            ],
            'expense' => [
                ['name' => 'view_expenses', 'display_name' => 'View Expenses'],
                ['name' => 'create_expense', 'display_name' => 'Create Expense'],
                ['name' => 'edit_expense', 'display_name' => 'Edit Expense'],
                ['name' => 'delete_expense', 'display_name' => 'Delete Expense'],
            ],
            'emi' => [
                ['name' => 'view_emi', 'display_name' => 'View EMI'],
                ['name' => 'create_emi', 'display_name' => 'Create EMI'],
                ['name' => 'edit_emi', 'display_name' => 'Edit EMI'],
                ['name' => 'delete_emi', 'display_name' => 'Delete EMI'],
                ['name' => 'record_payment', 'display_name' => 'Record EMI Payment'],
            ],
            'reports' => [
                ['name' => 'view_reports', 'display_name' => 'View Reports'],
                ['name' => 'export_reports', 'display_name' => 'Export Reports'],
            ],
            'parties' => [
                ['name' => 'view_parties', 'display_name' => 'View Parties'],
                ['name' => 'create_party', 'display_name' => 'Create Party'],
                ['name' => 'edit_party', 'display_name' => 'Edit Party'],
                ['name' => 'delete_party', 'display_name' => 'Delete Party'],
            ],
            'vehicle' => [
                ['name' => 'view_vehicles', 'display_name' => 'View Vehicles'],
                ['name' => 'create_vehicle', 'display_name' => 'Create Vehicle'],
                ['name' => 'edit_vehicle', 'display_name' => 'Edit Vehicle'],
                ['name' => 'delete_vehicle', 'display_name' => 'Delete Vehicle'],
            ],
            'driver' => [
                ['name' => 'view_drivers', 'display_name' => 'View Drivers'],
                ['name' => 'create_driver', 'display_name' => 'Create Driver'],
                ['name' => 'edit_driver', 'display_name' => 'Edit Driver'],
                ['name' => 'delete_driver', 'display_name' => 'Delete Driver'],
            ],
            'supplier' => [
                ['name' => 'view_suppliers', 'display_name' => 'View Suppliers'],
                ['name' => 'create_supplier', 'display_name' => 'Create Supplier'],
                ['name' => 'edit_supplier', 'display_name' => 'Edit Supplier'],
                ['name' => 'delete_supplier', 'display_name' => 'Delete Supplier'],
            ],
            'trader' => [
                ['name' => 'view_traders', 'display_name' => 'View Traders'],
                ['name' => 'create_trader', 'display_name' => 'Create Trader'],
                ['name' => 'edit_trader', 'display_name' => 'Edit Trader'],
                ['name' => 'delete_trader', 'display_name' => 'Delete Trader'],
            ],
            'company' => [
                ['name' => 'view_company', 'display_name' => 'View Company'],
                ['name' => 'create_company', 'display_name' => 'Create Company'],
                ['name' => 'edit_company', 'display_name' => 'Edit Company'],
                ['name' => 'delete_company', 'display_name' => 'Delete Company'],
            ],
            'branch' => [
                ['name' => 'view_branch', 'display_name' => 'View Branch'],
                ['name' => 'create_branch', 'display_name' => 'Create Branch'],
                ['name' => 'edit_branch', 'display_name' => 'Edit Branch'],
                ['name' => 'delete_branch', 'display_name' => 'Delete Branch'],
            ],
            'settings' => [
                ['name' => 'view_settings_financial_year', 'display_name' => 'View Financial Year Settings'],
                ['name' => 'view_settings_branch_default', 'display_name' => 'View Branch Default Settings'],
                ['name' => 'view_settings_account_limits', 'display_name' => 'View Account Limits Settings'],
            ],
        ];

        // Create permissions
        $permissions = [];
        foreach ($permissionsData as $module => $modulePermissions) {
            foreach ($modulePermissions as $perm) {
                $permissions[$perm['name']] = Permission::updateOrCreate(
                    ['name' => $perm['name']],
                    [
                        'display_name' => $perm['display_name'],
                        'module' => $module,
                        'is_active' => true,
                    ]
                );
            }
        }

        // Create roles and assign permissions
        $allPermissionIds = Permission::pluck('id', 'name');

        $superAdminRole = Role::updateOrCreate(
            ['name' => 'super_admin'],
            [
                'display_name' => 'Super Administrator',
                'description' => 'Super admin — full system access, bypasses all permission/menu checks',
                'is_active' => true,
            ]
        );
        $superAdminRole->permissions()->sync($allPermissionIds->values()->toArray());

        $adminRole = Role::updateOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'Administrator',
                'description' => 'Full system access',
                'is_active' => true,
            ]
        );
        $adminRole->permissions()->sync($allPermissionIds->values()->toArray());

        $managerRole = Role::updateOrCreate(
            ['name' => 'manager'],
            [
                'display_name' => 'Manager',
                'description' => 'Can manage operations and view reports',
                'is_active' => true,
            ]
        );
        $managerRole->permissions()->sync(
            $allPermissionIds->only([
                'view_dashboard',
                'view_trips', 'create_trip', 'edit_trip',
                'view_expenses', 'create_expense', 'edit_expense',
                'view_emi', 'view_reports',
                'view_parties', 'view_vehicles', 'view_drivers', 'view_suppliers', 'view_traders',
            ])->values()->toArray()
        );

        $operatorRole = Role::updateOrCreate(
            ['name' => 'operator'],
            [
                'display_name' => 'Operator',
                'description' => 'Can create and manage trips',
                'is_active' => true,
            ]
        );
        $operatorRole->permissions()->sync(
            $allPermissionIds->only([
                'view_dashboard',
                'view_trips', 'create_trip', 'edit_trip',
                'view_vehicles', 'view_drivers', 'view_parties',
            ])->values()->toArray()
        );

        $accountantRole = Role::updateOrCreate(
            ['name' => 'accountant'],
            [
                'display_name' => 'Accountant',
                'description' => 'Can manage expenses and EMI',
                'is_active' => true,
            ]
        );
        $accountantRole->permissions()->sync(
            $allPermissionIds->only([
                'view_dashboard',
                'view_expenses', 'create_expense', 'edit_expense',
                'view_emi', 'create_emi', 'edit_emi', 'record_payment',
                'view_reports',
            ])->values()->toArray()
        );

        $viewerRole = Role::updateOrCreate(
            ['name' => 'viewer'],
            [
                'display_name' => 'Viewer',
                'description' => 'Read-only access',
                'is_active' => true,
            ]
        );
        $viewerRole->permissions()->sync(
            $allPermissionIds->only([
                'view_dashboard',
                'view_trips', 'view_expenses', 'view_emi', 'view_reports',
                'view_parties', 'view_vehicles', 'view_drivers', 'view_suppliers', 'view_traders',
            ])->values()->toArray()
        );
    }
}
