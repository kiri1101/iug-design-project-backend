<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'uuid' => Str::uuid()->toString(),
            'first_name' => 'Jordan',
            'last_name' => 'Tukum',
            'email' => 'admin@admin.com',
            'phone' => '698377389',
            'password' => Hash::make('admin@admin01'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // save user role
        $user->roles()->attach(Role::CEO, [
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // create user profile
        $user->profile()->create([
            'uuid' => Str::uuid()->toString(),
            'department_id' => Department::EXECUTIVE,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
