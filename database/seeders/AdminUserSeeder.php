<?php

namespace Database\Seeders;

use App\Models\Login;
use App\Models\Menu;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /* Creates a super_admin user (admin@admin.com / admin@123) with
       full access — all roles, menus, and permissions assigned. */
    public function run(): void
    {
        // Create the User used for role/menu/permission relationships
        $user = User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name'     => 'Admin',
                'password' => bcrypt('admin@123'),
            ]
        );

        // Create the Login used for session-based auth
        Login::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'password'   => bcrypt('admin@123'),
                'role'       => 'super_admin',
                'status'     => true,
                'is_deleted' => false,
            ]
        );

        // Assign super_admin role
        $role = Role::where('name', 'super_admin')->first();
        if ($role) {
            $user->roles()->syncWithoutDetaching([$role->id]);
        }

        // Assign all menus (including payroll)
        $user->menus()->syncWithoutDetaching(Menu::pluck('id')->toArray());

        // Assign all permissions (including payroll)
        $user->permissions()->syncWithoutDetaching(Permission::pluck('id')->toArray());
    }
}
