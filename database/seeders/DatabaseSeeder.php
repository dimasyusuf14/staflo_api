<?php

namespace Database\Seeders;

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
        User::create([
            'name' => 'Direktur',
            'email' => 'direktur@example.com',
            'password' => bcrypt('password123'),
            'role' => 'director',
        ]);

        // Seed manager
        User::create([
            'name' => 'HR Manager',
            'email' => 'manager@example.com',
            'password' => bcrypt('password123'),
            'role' => 'manager',
        ]);

        // Seed staff
        User::create([
            'name' => 'Staff User',
            'email' => 'staff@example.com',
            'password' => bcrypt('password123'),
            'role' => 'staff',
        ]);
    }
}
