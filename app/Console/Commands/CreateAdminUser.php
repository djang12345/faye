<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create admin user for DPS system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Check if admin user already exists
        $existingAdmin = User::where('email', 'admin@dps.gov.ph')->first();
        
        if ($existingAdmin) {
            $this->error('Admin user already exists!');
            return 1;
        }

        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@dps.gov.ph',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        $this->info('Admin user created successfully!');
        $this->info('Email: admin@dps.gov.ph');
        $this->info('Password: admin123');
        $this->info('Role: admin');

        return 0;
    }
}
