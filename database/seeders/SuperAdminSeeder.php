<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\Role as RoleName;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'suleosman73@gmail.com'],
            [
                'name' => 'Suleiman Osman',
                'phone' => '254718772203',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $admin->syncRoles([RoleName::ADMIN]);
    }
}
