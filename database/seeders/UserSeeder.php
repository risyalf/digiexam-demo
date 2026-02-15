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
        $adminPassword = Hash::make("admin");
        $guruPassword = Hash::make("guru");
        $siswaPassword = Hash::make("siswa");


        $participantGroup = ParticipantGroup::create([
            "name" => "IPA X-1",
        ]);
        $participantGroup = ParticipantGroup::create([
            "name" => "IPA X-2",
        ]);

        // admin & guru (1 query)
        User::upsert(
            [
                [
                    "name" => "admin",
                    "email" => "admin@mail.com",
                    "nis" => "admin",
                    "password" => $adminPassword,
                    "created_at" => $now,
                    "updated_at" => $now,
                ],
                [
                    "name" => "guru",
                    "email" => "guru@mail.com",
                    "nis" => null,
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
        $participant = [];

        $groups = ParticipantGroup::pluck('id')->toArray();

        for ($i = 0; $i < 20; $i++) {
            $userId = Str::uuid7();
            $siswa[] = [
                "id" => $userId,
                "name" => "siswa-$i",
                "email" => "siswa_$i@mail.com",
                "nis" => $i,
                "password" => $siswaPassword,
                "created_at" => $now,
                "updated_at" => $now,
            ];

            foreach ($groups as $key => $group) {
                $participant[] = [
                    'id' => Str::uuid7(),
                    'user_id' => $userId,
                    'participant_group_id' => $group,
                    "created_at" => $now,
                    "updated_at" => $now,
                ];
            }
        }

        User::insert($siswa);
        Participant::insert($participant);
    }
}
