<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = [
            'name' => 'shaheen',
            'email' => 'shaheen@gmail.com',
            'password' => Hash::make('ASDasdasd'), // Default password
            'is_active' => 1,
            'role' => '2',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        User::create($user);
    }
}
