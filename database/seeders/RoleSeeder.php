<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::insert([
            [
                'uuid' => Str::uuid()->toString(),
                'name' => 'Ceo',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'name' => 'Director of HR Department',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'name' => 'Director of Technical Department',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'name' => 'Director of Finance Department',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'name' => 'Manager of HR Department',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'name' => 'Manager of Technical Department',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'name' => 'Manager of Finance Department',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'name' => 'Employee',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
