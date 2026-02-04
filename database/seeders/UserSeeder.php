<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(UserFactory $userFactory): void
    {
        $users = [
            [
                "name" => "admin",
                "email" => "admin@mail.com",
                "password" => "admin",
            ],
            [
                "name" => "guru",
                "email" => "guru@mail.com",
                "password" => "guru",
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ["email" => $user["email"]],
                [
                    "name" => $user["name"],
                    "password" => Hash::make($user["password"]),
                ],
            );
        }

        // siswa
        User::factory()
            ->count(20)
            ->sequence(
                fn($sequence) => [
                    "name" => "siswa-{$sequence->index}",
                    "email" => "siswa_{$sequence->index}@mail.com",
                    "password" => Hash::make("siswa"),
                ],
            )
            ->create();
    }
}
