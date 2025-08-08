<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleBasedUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@dps.gov.ph',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // Create Employee Users
        User::create([
            'name' => 'John Employee',
            'email' => 'employee@dps.gov.ph',
            'password' => Hash::make('employee123'),
            'role' => 'employee',
        ]);

        User::create([
            'name' => 'Jane Employee',
            'email' => 'jane.employee@dps.gov.ph',
            'password' => Hash::make('employee123'),
            'role' => 'employee',
        ]);

        User::create([
            'name' => 'Mike Employee',
            'email' => 'mike.employee@dps.gov.ph',
            'password' => Hash::make('employee123'),
            'role' => 'employee',
        ]);
    }
}
