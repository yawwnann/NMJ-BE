<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'adminAcc1@gmail.com'],
            [
                'name' => 'Admin',
                'email' => 'adminAcc1@gmail.com',
                'password' => Hash::make('adminAcc1'),
                'role' => 'admin',
            ]
        );
    }
}