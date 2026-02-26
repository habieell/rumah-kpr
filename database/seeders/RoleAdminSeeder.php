<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;


class RoleAdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::create([
            'name' => 'admin'
        ]);

        $lenderRole = Role::create([
            'name' => 'lender'
        ]);

        $agentRole = Role::create([
            'name' => 'agent'
        ]);

        $customerRole = Role::create([
            'name' => 'customer'
        ]);

        $user = User::create([
            'name' => 'Joko Kopling',
            'email' => 'xoos110@gmail.com',
            'phone' => '081234567890',
            'photo' => 'joko.png',
            'password' => bcrypt('kostum7680')
        ]);

        $user->assignRole($adminRole);
    }
}
