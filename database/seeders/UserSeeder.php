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
        # Create CEO
        $ceo = User::create([
            'uuid' => Str::uuid()->toString(),
            'first_name' => 'jordan',
            'last_name' => 'tukum',
            'email' => 'admin@admin.com',
            'phone' => '698377389',
            'password' => Hash::make('admin@admin01'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        # Create Human Resource Director
        $dhr = User::create([
            'uuid' => Str::uuid()->toString(),
            'first_name' => 'stephan',
            'last_name' => 'otu',
            'email' => 'stef.otu@gmail.com',
            'phone' => '698377350',
            'password' => Hash::make('stef@dhr01'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        # Create Technical Director
        $dt = User::create([
            'uuid' => Str::uuid()->toString(),
            'first_name' => 'dimitri',
            'last_name' => 'romanov',
            'email' => 'dim.nov02@gmail.com',
            'phone' => '698377351',
            'password' => Hash::make('roman@dtDim03'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        # Create Finance Director
        $df = User::create([
            'uuid' => Str::uuid()->toString(),
            'first_name' => 'mashalah',
            'last_name' => 'rhadja',
            'email' => 'mash.raj@hotmail.com',
            'phone' => '698377352',
            'password' => Hash::make('mash@dfRad03'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        # Create Product Director
        $dp = User::create([
            'uuid' => Str::uuid()->toString(),
            'first_name' => 'ranita',
            'last_name' => 'seilinou',
            'email' => 'ranita05@hotmail.com',
            'phone' => '698377353',
            'password' => Hash::make('ranita@dpSei05'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // save user role
        $ceo->roles()->attach(Role::CEO, [
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $dhr->roles()->attach(Role::DHR, [
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $dt->roles()->attach(Role::DT, [
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $df->roles()->attach(Role::DF, [
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $dp->roles()->attach(Role::DP, [
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // create user profile
        $ceo->profile()->create([
            'uuid' => Str::uuid()->toString(),
            'department_id' => Department::EXECUTIVE,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $dhr->profile()->create([
            'uuid' => Str::uuid()->toString(),
            'department_id' => Department::HR,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $dt->profile()->create([
            'uuid' => Str::uuid()->toString(),
            'department_id' => Department::TECHNICAL,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $df->profile()->create([
            'uuid' => Str::uuid()->toString(),
            'department_id' => Department::FINANCIAL,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $dp->profile()->create([
            'uuid' => Str::uuid()->toString(),
            'department_id' => Department::PRODUCT,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
