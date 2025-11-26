<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::query()->firstOrCreate([
            'username' => 'admin1',
            'password' => Hash::make('password'),
        ]);

        Admin::query()->firstOrCreate([
            'username' => 'admin2',
            'password' => Hash::make('password'),
        ]);
    }
}
