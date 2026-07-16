<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /* Run order: menus → permissions → banks → admin user.
       RolesAndPermissionsSeeder must run before AdminUserSeeder so
       the roles, permissions, and menus already exist to assign. */
    public function run(): void
    {
        $this->call([
            MenusSeeder::class,
            RolesAndPermissionsSeeder::class,
            BankSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
