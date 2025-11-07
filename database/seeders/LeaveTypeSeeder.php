<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Death (Parent or/and spouse), Child birth, Illness, Work leave, Absence during work hours, Absence for a complete work day

        DB::table('leave_types')->insert([
            [
                'uuid' => Str::uuid()->toString(),
                'name' => 'Death (Parent/Spouse)',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'name' => 'Child birth',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'name' => 'Illness',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'name' => 'Work leave',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'name' => 'Absence during work hours',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'name' => 'Absence for a complete work day',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
