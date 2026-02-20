<?php

namespace Database\Seeders;

use App\Models\Participant;
use App\Models\ParticipantGroup;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // password di-hash SEKALI
        $adminPassword = Hash::make("RootSwadaya!123");

        // admin & guru (1 query)
        User::upsert(
            [
                [
                    "name" => "Admin SMK Swadaya",
                    "email" => "swadayasemarang@gmail.com",
                    "nis" => "admin",
                    "password" => $adminPassword,
                    "created_at" => $now,
                    "updated_at" => $now,
                ],
            ],
            ["email"],
            ["name", "password", "updated_at"],
        );
    }
}
