<?php

namespace Database\Seeders;

use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed positions first
        $this->call(PositionSeeder::class);

        // Get positions
        $directorPosition = Position::where('code', 'director')->first();
        $managerPosition = Position::where('code', 'manager')->first();
        $webDeveloperPosition = Position::where('code', 'web_developer')->first();

        // Seed director
        User::create([
            'name' => 'Direktur',
            'email' => 'direktur@example.com',
            'password' => bcrypt('?Staflo2026!'),
            'role' => 'director',
            'position_id' => $directorPosition?->id,
        ]);


        // Seed managers
        User::create([
            'name' => 'HR Manager',
            'email' => 'manager@example.com',
            'password' => bcrypt('?Staflo2026!'),
            'role' => 'manager',
            'position_id' => $managerPosition?->id,
        ]);
        User::create([
            'name' => 'Finance Manager',
            'email' => 'finance.manager@example.com',
            'password' => bcrypt('?Staflo2026!'),
            'role' => 'manager',
            'position_id' => $managerPosition?->id,
        ]);
        User::create([
            'name' => 'IT Manager',
            'email' => 'it.manager@example.com',
            'password' => bcrypt('?Staflo2026!'),
            'role' => 'manager',
            'position_id' => $managerPosition?->id,
        ]);
        User::create([
            'name' => 'Marketing Manager',
            'email' => 'marketing.manager@example.com',
            'password' => bcrypt('?Staflo2026!'),
            'role' => 'manager',
            'position_id' => $managerPosition?->id,
        ]);

        // Seed staff
        User::create([
            'name' => 'Staff User',
            'email' => 'staff@example.com',
            'password' => bcrypt('?Staflo2026!'),
            'role' => 'staff',
            'position_id' => $webDeveloperPosition?->id,
        ]);
    }
}
