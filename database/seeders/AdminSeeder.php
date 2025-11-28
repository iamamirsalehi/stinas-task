<?php

namespace Database\Seeders;

use App\Enums\Admin as EnumsAdmin;
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
            'username' => EnumsAdmin::Admin1->value,
            'password' => Hash::make('password'),
        ]);

        Admin::query()->firstOrCreate([
            'username' => EnumsAdmin::Admin2->value,
            'password' => Hash::make('password'),
        ]);
    }
}
