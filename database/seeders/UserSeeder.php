<?php

namespace Database\Seeders;

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
        $adminPassword = Hash::make("admin");
        $guruPassword = Hash::make("guru");
        $siswaPassword = Hash::make("siswa");

        // admin & guru (1 query)
        User::upsert(
            [
                [
                    "name" => "admin",
                    "email" => "admin@mail.com",
                    "nik" => "admin",
                    "password" => $adminPassword,
                    "created_at" => $now,
                    "updated_at" => $now,
                ],
                [
                    "name" => "guru",
                    "email" => "guru@mail.com",
                    "nik" => null,
                    "password" => $guruPassword,
                    "created_at" => $now,
                    "updated_at" => $now,
                ],
            ],
            ["email"],
            ["name", "password", "updated_at"],
        );

        // siswa (1 query)
        $siswa = [];

        for ($i = 0; $i < 20; $i++) {
            $siswa[] = [
                "id" => Str::uuid7(),
                "name" => "siswa-$i",
                "email" => "siswa_$i@mail.com",
                "password" => $siswaPassword,
                "created_at" => $now,
                "updated_at" => $now,
            ];
        }

        User::insert($siswa);
    }
}
