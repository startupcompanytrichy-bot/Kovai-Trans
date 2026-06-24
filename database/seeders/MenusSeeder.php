<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenusSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            [
                'name' => 'dashboard',
                'display_name' => 'Dashboard',
                'icon' => 'ti-home',
                'route' => 'dashboard',
                'order' => 1,
            ],
            [
                'name' => 'trips',
                'display_name' => 'Trip Management',
                'icon' => 'ti-location-arrow',
                'route' => 'trip',
                'order' => 2,
            ],
            [
                'name' => 'expenses',
                'display_name' => 'Expenses',
                'icon' => 'ti-receipt',
                'route' => 'expense',
                'order' => 3,
            ],
            [
                'name' => 'vehicle_emi',
                'display_name' => 'Vehicle EMI',
                'icon' => 'ti-calendar',
                'route' => 'emi',
                'order' => 4,
            ],
            [
                'name' => 'reports',
                'display_name' => 'Reports',
                'icon' => 'ti-bar-chart',
                'route' => 'reports',
                'order' => 5,
            ],
            [
                'name' => 'parties',
                'display_name' => 'Parties',
                'icon' => 'ti-layers',
                'route' => 'parties',
                'order' => 6,
            ],
            [
                'name' => 'vehicles',
                'display_name' => 'Vehicles',
                'icon' => 'ti-truck',
                'route' => 'vehicle',
                'order' => 7,
            ],
            [
                'name' => 'drivers',
                'display_name' => 'Drivers',
                'icon' => 'ti-id-badge',
                'route' => 'driver',
                'order' => 8,
            ],
            [
                'name' => 'suppliers',
                'display_name' => 'Suppliers',
                'icon' => 'ti-user',
                'route' => 'supplier',
                'order' => 9,
            ],
            [
                'name' => 'traders',
                'display_name' => 'Traders',
                'icon' => 'ti-package',
                'route' => 'trader',
                'order' => 10,
            ],
            [
                'name' => 'organization',
                'display_name' => 'Organization',
                'icon' => 'ti-layout-grid2-alt',
                'route' => null,
                'order' => 11,
            ],
            [
                'name' => 'users',
                'display_name' => 'Users',
                'icon' => 'ti-lock',
                'route' => 'user-permissions.index',
                'order' => 12,
            ],
            [
                'name' => 'screen_permissions',
                'display_name' => 'Screen Permissions',
                'icon' => 'ti-lock',
                'route' => 'user-permissions.authorization',
                'order' => 13,
            ],
            [
                'name' => 'settings',
                'display_name' => 'Settings',
                'icon' => 'ti-settings',
                'route' => 'settings',
                'order' => 14,
            ],
        ];

        foreach ($menus as $menu) {
            Menu::updateOrCreate(
                ['name' => $menu['name']],
                [
                    'display_name' => $menu['display_name'],
                    'icon' => $menu['icon'],
                    'route' => $menu['route'],
                    'order' => $menu['order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
