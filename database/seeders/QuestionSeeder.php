<?php

namespace Database\Seeders;

use App\Enum\AssessmentStatus;
use App\Enum\AssessmentType;
use App\Models\Assessment;
use App\Models\Module;
use App\Models\Topic;
use App\Models\UserGroup;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userGroup = UserGroup::create([
            'name' => 'IPA X-1'
        ]);

        $module = Module::create([
            'name' => 'Matematika'
        ]);

        $topic = Topic::create([
            'module_id' => $module->id,
            'name' => 'UTS 2026',
            'description' => 'Untuk uts bulan maret 2026'
        ]);

        Assessment::create([
            'user_group_id' => $userGroup->id,
            'name' => 'Uji Coba Soal',
            'description' => 'Untuk Uji Coba Soal',
            'start_date' => Carbon::now()->toDateTimeString(),
            'end_date' => Carbon::now()->addDays(30)->toDateTimeString(),
            'time_test' => 90,
            'correct_point' => 1,
            'wrong_point' => 0,
            'empty_point' => 0,
            'module_id' => $module->id,
            'topic_id' => $topic->id,
            'type' => AssessmentType::PILIHAN_GANDA,
            'difficulty_level' => 1,
            'total_question' => 10,
            'total_answer' => 10,
            'status' => AssessmentStatus::BELUM_DIMULAI,
        ]);
    }
}
