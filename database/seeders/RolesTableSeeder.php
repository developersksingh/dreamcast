<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Date;
use App\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'name' => 'Admin',
                'description' => 'Administrator with full access',
                'created_at' => Date::now(),
                'updated_at' => Date::now()
            ],
            [
                'name' => 'User',
                'description' => 'Regular user with limited access',
                'created_at' => Date::now(),
                'updated_at' => Date::now()
            ],
            [
                'name' => 'Guest',
                'description' => 'Guest user with restricted access',
                'created_at' => Date::now(),
                'updated_at' => Date::now()
            ]
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
