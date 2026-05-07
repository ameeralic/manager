<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::firstOrCreate(['name' => 'Ameer'], ['pin' => '080524']);
        User::firstOrCreate(['name' => 'Jinsi'], ['pin' => '100395']);
    }
}
