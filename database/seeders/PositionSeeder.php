<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Level 1 - Director
        Position::firstOrCreate(
            ['code' => 'director'],
            [
                'name' => 'Director',
                'level' => 1,
                'description' => 'Direktur - Akses penuh sistem',
                'is_active' => true,
            ]
        );

        // Level 2 - Manager/Supervisor
        $level2Positions = [
            ['code' => 'manager', 'name' => 'Manager', 'description' => 'Manager - Kelola staff dan data'],
            ['code' => 'supervisor', 'name' => 'Supervisor', 'description' => 'Supervisor - Kelola tim'],
            ['code' => 'team_lead', 'name' => 'Team Lead', 'description' => 'Team Lead - Pimpin tim kecil'],
        ];

        foreach ($level2Positions as $position) {
            Position::firstOrCreate(
                ['code' => $position['code']],
                [
                    'name' => $position['name'],
                    'level' => 2,
                    'description' => $position['description'],
                    'is_active' => true,
                ]
            );
        }

        // Level 3 - Staff
        $level3Positions = [
            ['code' => 'mobile_developer', 'name' => 'Mobile Developer', 'description' => 'Developer - Mobile platforms'],
            ['code' => 'web_developer', 'name' => 'Web Developer', 'description' => 'Developer - Web applications'],
            ['code' => 'backend_developer', 'name' => 'Backend Developer', 'description' => 'Developer - Backend systems'],
            ['code' => 'frontend_developer', 'name' => 'Frontend Developer', 'description' => 'Developer - Frontend UI/UX'],
            ['code' => 'qa_engineer', 'name' => 'QA Engineer', 'description' => 'Quality Assurance - Testing'],
            ['code' => 'devops_engineer', 'name' => 'DevOps Engineer', 'description' => 'DevOps - Infrastructure'],
            ['code' => 'product_manager', 'name' => 'Product Manager', 'description' => 'Product - Management'],
            ['code' => 'designer', 'name' => 'UI/UX Designer', 'description' => 'Design - User interface'],
            ['code' => 'marketing', 'name' => 'Marketing Specialist', 'description' => 'Marketing - Campaigns'],
            ['code' => 'sales', 'name' => 'Sales Executive', 'description' => 'Sales - Client relations'],
            ['code' => 'hr', 'name' => 'HR Specialist', 'description' => 'HR - Human Resources'],
            ['code' => 'finance', 'name' => 'Finance Specialist', 'description' => 'Finance - Accounting'],
            ['code' => 'staff', 'name' => 'Staff', 'description' => 'Staff - General'],
        ];

        foreach ($level3Positions as $position) {
            Position::firstOrCreate(
                ['code' => $position['code']],
                [
                    'name' => $position['name'],
                    'level' => 3,
                    'description' => $position['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
