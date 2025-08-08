<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateEmployeeUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-employee';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create employee user for DPS system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Check if employee user already exists
        $existingEmployee = User::where('email', 'employee@dps.gov.ph')->first();
        
        if ($existingEmployee) {
            $this->error('Employee user already exists!');
            return 1;
        }

        // Create employee user
        $employee = User::create([
            'name' => 'Employee User',
            'email' => 'employee@dps.gov.ph',
            'password' => Hash::make('employee123'),
            'role' => 'employee',
        ]);

        $this->info('Employee user created successfully!');
        $this->info('Email: employee@dps.gov.ph');
        $this->info('Password: employee123');
        $this->info('Role: employee');

        return 0;
    }
}
