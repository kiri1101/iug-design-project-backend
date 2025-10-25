<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('departments')->insert([
            [
                'uuid' => Str::uuid()->toString(),
                'name' => 'Executive board',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'name' => 'Technical department',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'name' => 'Product department',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'name' => 'Financial department',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'name' => 'HR department',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
