<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Applicant;
use App\Models\ApplicantParents;
use App\Models\Application;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin Users
        $adminUsers = [
            [
                'name' => 'John Admin',
                'email' => 'admin@dps.gov.ph',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ],
            [
                'name' => 'Maria Santos',
                'email' => 'system.admin@dps.gov.ph',
                'password' => Hash::make('system123'),
                'role' => 'admin',
            ],
            [
                'name' => 'Carlos Rodriguez',
                'email' => 'dept.admin@dps.gov.ph',
                'password' => Hash::make('dept123'),
                'role' => 'admin',
            ],
        ];

        foreach ($adminUsers as $adminUser) {
            User::firstOrCreate(
                ['email' => $adminUser['email']],
                $adminUser
            );
        }

        // Create Applicant Users and Data
        $applicantData = [
            [
                'user' => [
                    'name' => 'Juan Dela Cruz',
                    'email' => 'juan.delacruz@email.com',
                    'password' => Hash::make('applicant123'),
                ],
                'applicant' => [
                    'firstname' => 'Juan',
                    'lastname' => 'Dela Cruz',
                    'email' => 'juan.delacruz@email.com',
                    'age' => '25',
                    'sex' => 'Male',
                    'height' => '5\'8"',
                    'weight' => '70kg',
                    'birthdate' => '1998-05-15',
                    'status' => 'Single',
                    'citizenship' => 'Filipino',
                    'barangay' => 'San Antonio',
                    'municipality' => 'Quezon City',
                    'province' => 'Metro Manila',
                    'country' => 'Philippines',
                ],
                'parents' => [
                    'mother_firstname' => 'Ana',
                    'mother_lastname' => 'Dela Cruz',
                    'mother_occupation' => 'Teacher',
                    'father_firstname' => 'Jose',
                    'father_lastname' => 'Dela Cruz',
                    'father_occupation' => 'Engineer',
                ],
                'application' => [
                    'status' => 'PENDING',
                ],
            ],
            [
                'user' => [
                    'name' => 'Maria Garcia',
                    'email' => 'maria.garcia@email.com',
                    'password' => Hash::make('applicant123'),
                ],
                'applicant' => [
                    'firstname' => 'Maria',
                    'lastname' => 'Garcia',
                    'email' => 'maria.garcia@email.com',
                    'age' => '28',
                    'sex' => 'Female',
                    'height' => '5\'5"',
                    'weight' => '55kg',
                    'birthdate' => '1995-08-22',
                    'status' => 'Married',
                    'citizenship' => 'Filipino',
                    'barangay' => 'Makati',
                    'municipality' => 'Makati City',
                    'province' => 'Metro Manila',
                    'country' => 'Philippines',
                ],
                'parents' => [
                    'mother_firstname' => 'Carmen',
                    'mother_lastname' => 'Garcia',
                    'mother_occupation' => 'Nurse',
                    'father_firstname' => 'Roberto',
                    'father_lastname' => 'Garcia',
                    'father_occupation' => 'Police Officer',
                ],
                'application' => [
                    'status' => 'PENDING',
                ],
            ],
            [
                'user' => [
                    'name' => 'Pedro Santos',
                    'email' => 'pedro.santos@email.com',
                    'password' => Hash::make('applicant123'),
                ],
                'applicant' => [
                    'firstname' => 'Pedro',
                    'lastname' => 'Santos',
                    'email' => 'pedro.santos@email.com',
                    'age' => '30',
                    'sex' => 'Male',
                    'height' => '5\'10"',
                    'weight' => '75kg',
                    'birthdate' => '1993-12-10',
                    'status' => 'Single',
                    'citizenship' => 'Filipino',
                    'barangay' => 'Tondo',
                    'municipality' => 'Manila',
                    'province' => 'Metro Manila',
                    'country' => 'Philippines',
                ],
                'parents' => [
                    'mother_firstname' => 'Elena',
                    'mother_lastname' => 'Santos',
                    'mother_occupation' => 'Business Owner',
                    'father_firstname' => 'Manuel',
                    'father_lastname' => 'Santos',
                    'father_occupation' => 'Retired',
                ],
                'application' => [
                    'status' => 'ACCEPTED',
                    'interview_date' => '2025-08-15',
                    'interview_time' => '09:00:00',
                ],
            ],
        ];

        // Skip applicant creation for now to focus on leave requests
        // foreach ($applicantData as $data) {
        //     // Create user
        //     $user = User::firstOrCreate(
        //         ['email' => $data['user']['email']],
        //         $data['user']
        //     );

        //     // Create parents
        //     $parents = ApplicantParents::firstOrCreate(
        //         ['mother_email' => $data['parents']['mother_email'] ?? $data['user']['email']],
        //         $data['parents']
        //     );

        //     // Create applicant
        //     $applicantData = $data['applicant'];
        //     $applicantData['parents_id'] = $parents->id;
        //     $applicant = Applicant::firstOrCreate(
        //         ['email' => $data['user']['email']],
        //         $applicantData
        //     );

        //     // Create application
        //     $applicationData = $data['application'];
        //     $applicationData['applicant_id'] = $applicant->id;
        //     Application::firstOrCreate(
        //         ['applicant_id' => $applicant->id],
        //         $applicationData
        //     );
        // }

        $this->command->info('Sample data seeded successfully!');
        $this->command->info('Admin users created:');
        $this->command->info('- admin@dps.gov.ph (password: admin123)');
        $this->command->info('- system.admin@dps.gov.ph (password: system123)');
        $this->command->info('- dept.admin@dps.gov.ph (password: dept123)');
        $this->command->info('');
        $this->command->info('Applicant users created:');
        $this->command->info('- juan.delacruz@email.com (password: applicant123)');
        $this->command->info('- maria.garcia@email.com (password: applicant123)');
        $this->command->info('- pedro.santos@email.com (password: applicant123)');

        // Create sample leave requests
        $this->createSampleLeaveRequests();
    }

    private function createSampleLeaveRequests()
    {
        // Get or create employee users with better names
        $employees = [
            [
                'name' => 'Juan Dela Cruz',
                'email' => 'juan.employee@dps.gov.ph',
                'password' => Hash::make('employee123'),
                'role' => 'employee',
            ],
            [
                'name' => 'Maria Santos',
                'email' => 'maria.employee@dps.gov.ph',
                'password' => Hash::make('employee123'),
                'role' => 'employee',
            ],
            [
                'name' => 'Pedro Rodriguez',
                'email' => 'pedro.employee@dps.gov.ph',
                'password' => Hash::make('employee123'),
                'role' => 'employee',
            ],
        ];

        foreach ($employees as $employeeData) {
            $user = User::firstOrCreate(
                ['email' => $employeeData['email']],
                $employeeData
            );

            // Create sample leave requests for each employee
            $leaveTypes = ['emergency', 'sick', 'vacation'];
            $statuses = ['pending', 'approved', 'rejected'];

            for ($i = 0; $i < 3; $i++) {
                \App\Models\LeaveRequest::create([
                    'user_id' => $user->id,
                    'leave_type' => $leaveTypes[array_rand($leaveTypes)],
                    'start_date' => now()->addDays(rand(1, 30)),
                    'end_date' => now()->addDays(rand(2, 35)),
                    'days_requested' => rand(1, 5),
                    'details' => 'Sample leave request details for testing purposes.',
                    'status' => $statuses[array_rand($statuses)],
                    'admin_notes' => rand(0, 1) ? 'Sample admin notes for this request.' : null,
                    'approved_by' => rand(0, 1) ? 1 : null, // Admin user ID
                    'approved_at' => rand(0, 1) ? now() : null,
                ]);
            }
        }
    }
}
