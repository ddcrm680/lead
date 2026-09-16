<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::where('slug', 'super-admin')->firstOrFail();

        User::updateOrCreate(
            [
                'email' => env('ADMIN_EMAIL', 'admin@example.com'),
            ],
            [
                'name' => env('ADMIN_NAME', 'Super Admin'),
                'role_id' => $role->id,
                'is_active' => true,
                'password' => env('ADMIN_PASSWORD', 'password'),
            ]
        );
    }
}