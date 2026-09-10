<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'role' => 'admin',
            'email_verified_at' => now(),
            'password' => bcrypt('password')
        ]);
        User::create([
            'name' => 'User 1',
            'email' => 'user1@user.com',
            'role' => 'user',
            'email_verified_at' => now(),
            'password' => bcrypt('password')
        ]);
        User::create([
            'name' => 'User 2',
            'email' => 'user2@user.com',
            'role' => 'user',
            'email_verified_at' => now(),
            'password' => bcrypt('password')
        ]);
    }
}
