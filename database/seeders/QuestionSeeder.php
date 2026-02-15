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
        $module = Module::create([
            "name" => "UAS 2026",
        ]);

        Topic::create([
            "module_id" => $module->id,
            "name" => "BHS INDONESIA",
            "description" => "Untuk uas bulan maret 2026",
        ]);
    }
}
