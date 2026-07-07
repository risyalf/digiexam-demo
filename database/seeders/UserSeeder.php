<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $adminPassword = Hash::make("admin");

        User::create(
            [
                "name" => "Admin",
                "email" => "admin@mail.com",
                "nis" => "admin",
                "password" => $adminPassword,
                "created_at" => $now,
                "updated_at" => $now,
            ]
        );
    }
}
