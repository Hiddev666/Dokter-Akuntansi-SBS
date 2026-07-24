<?php

namespace Database\Seeders;

use App\Models\User;
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
            [
                'name' => 'Admin',
                'email' => 'admin@sistem.test',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($users as $user) {
            $createdUser = User::create($user);
            $createdUser->assignRole('admin');
        }
    }
}
