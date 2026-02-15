<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Participant;
use App\Models\ParticipantGroup;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ParticipantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $participantGroup = ParticipantGroup::create([
            "name" => "IPA X-1",
        ]);
        $participantGroup = ParticipantGroup::create([
            "name" => "IPA X-2",
        ]);

        $groups = ParticipantGroup::all();
        $userIds = User::role('siswa')
                    ->pluck('id')
                    ->toArray();

        $participant = [];

        $moduleId = Module::first()->id;

        foreach ($userIds as $userId) {
            foreach ($groups as $group) {
                $participant[] = [
                    'id' => Str::uuid7(),
                    'module_id' => $moduleId,
                    'user_id' => $userId,
                    'participant_group_id' => $group->id,
                    "created_at" => Carbon::now()->toDateTimeString(),
                    "updated_at" => Carbon::now()->toDateTimeString(),
                ];
            }
        }

        Participant::insert($participant);
    }
}
