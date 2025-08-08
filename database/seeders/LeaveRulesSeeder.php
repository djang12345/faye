<?php

namespace Database\Seeders;

use App\Models\LeaveRule;
use Illuminate\Database\Seeder;

class LeaveRulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $leaveRules = [
            [
                'leave_type' => 'emergency',
                'display_name' => 'Emergency Leave',
                'default_credits' => 5,
                'description' => 'Emergency leave for urgent personal matters',
                'is_active' => true,
            ],
            [
                'leave_type' => 'sick',
                'display_name' => 'Sick Leave',
                'default_credits' => 5,
                'description' => 'Medical leave for health-related issues',
                'is_active' => true,
            ],
            [
                'leave_type' => 'vacation',
                'display_name' => 'Vacation Leave',
                'default_credits' => 10,
                'description' => 'Annual vacation leave for rest and recreation',
                'is_active' => true,
            ],
        ];

        foreach ($leaveRules as $rule) {
            LeaveRule::updateOrCreate(
                ['leave_type' => $rule['leave_type']],
                $rule
            );
        }
    }
}
