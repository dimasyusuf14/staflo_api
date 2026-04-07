<?php

namespace Database\Seeders;

use App\Models\Bucket;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BucketSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $director = User::where('role', 'director')->first();

        $buckets = [
            [
                'name' => 'Human Resources',
                'description' => 'Bucket untuk tugas-tugas terkait HR dan pengelolaan SDM',
                'created_by' => $director?->id,
            ],
            [
                'name' => 'Finance',
                'description' => 'Bucket untuk tugas-tugas terkait keuangan dan akuntansi',
                'created_by' => $director?->id,
            ],
            [
                'name' => 'IT Development',
                'description' => 'Bucket untuk tugas-tugas pengembangan sistem dan teknologi',
                'created_by' => $director?->id,
            ],
            [
                'name' => 'Marketing',
                'description' => 'Bucket untuk tugas-tugas pemasaran dan promosi',
                'created_by' => $director?->id,
            ],
        ];

        foreach ($buckets as $bucket) {
            Bucket::create($bucket);
        }
    }
}
