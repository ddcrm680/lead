<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'id' => 1,
                'name' => 'Super Administrator',
                'slug' => 'super-admin',
                'is_active' => true,
            ],
            [
                'id' => 2,
                'name' => 'Administrator',
                'slug' => 'admin',
                'is_active' => true,
            ],
            [
                'id' => 3,
                'name' => 'Sales Manager',
                'slug' => 'sales-manager',
                'is_active' => true,
            ],
            [
                'id' => 4,
                'name' => 'Sales Agent',
                'slug' => 'sales-agent',
                'is_active' => true,
            ],
            [
                'id' => 5,
                'name' => 'Lead Manager',
                'slug' => 'lead-manager',
                'is_active' => true,
            ],
            [
                'id' => 6,
                'name' => 'Viewer',
                'slug' => 'viewer',
                'is_active' => true,
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }
    }
}
