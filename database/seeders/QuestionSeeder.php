<?php

namespace Database\Seeders;

use App\Enum\AssessmentStatus;
use App\Enum\AssessmentType;
use App\Models\Assessment;
use App\Models\AssessmentParticipantGroup;
use App\Models\Module;
use App\Models\Topic;
use App\Models\ParticipantGroup;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $participantGroup = ParticipantGroup::create([
            "name" => "IPA X-1",
        ]);

        $module = Module::create([
            "name" => "Matematika",
        ]);

        $topic = Topic::create([
            "module_id" => $module->id,
            "name" => "UTS 2026",
            "description" => "Untuk uts bulan maret 2026",
        ]);

        $assessment = Assessment::create([
            "name" => "Uji Coba Soal",
            "description" => "Untuk Uji Coba Soal",
            "start_date" => Carbon::now()->toDateTimeString(),
            "end_date" => Carbon::now()->addDays(30)->toDateTimeString(),
            "time_test" => 90,
            "correct_point" => 1,
            "wrong_point" => 0,
            "empty_point" => 0,
            "module_id" => $module->id,
            "topic_id" => $topic->id,
            "type" => AssessmentType::PILIHAN_GANDA,
            "total_question" => 10,
            "total_answer" => 10,
            "status" => AssessmentStatus::BELUM_DIMULAI,
        ]);

        AssessmentParticipantGroup::create([
            'assessment_id' => $assessment->id,
            'participant_group_id' => $participantGroup->id,
        ]);
    }
}
