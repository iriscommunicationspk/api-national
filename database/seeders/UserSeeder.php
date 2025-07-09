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
        $users = [
            // Admin user
            [
                'name' => 'admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('password123'),
                'is_active' => 1,
                'role' => 2, // Admin role
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Regular user 1
            [
                'name' => 'user1',
                'email' => 'user1@gmail.com',
                'password' => Hash::make('password123'),
                'is_active' => 1,
                'role' => 1, // Regular user role
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Regular user 2
            [
                'name' => 'user2',
                'email' => 'user2@gmail.com',
                'password' => Hash::make('password123'),
                'is_active' => 1,
                'role' => 1, // Regular user role
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Original user
            [
                'name' => 'shaheen',
                'email' => 'shaheen@gmail.com',
                'password' => Hash::make('ASDasdasd'),
                'is_active' => 1,
                'role' => 2, // Admin role
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(['email' => $user['email']], $user);
        }
    }
}
