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
                'code' => 'ceo',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'name' => 'Director of HR Department',
                'code' => 'dhr',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'name' => 'Director of Technical Department',
                'code' => 'dt',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'name' => 'Director of Finance Department',
                'code' => 'df',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'name' => 'Director of Product Department',
                'code' => 'dp',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'name' => 'Manager of HR Department',
                'code' => 'mhr',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'name' => 'Manager of Technical Department',
                'code' => 'mt',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'name' => 'Manager of Finance Department',
                'code' => 'mf',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'name' => 'Manager of Product Department',
                'code' => 'mp',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'name' => 'Employee',
                'code' => 'emp',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
