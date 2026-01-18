<?php

namespace App\Console\Commands;

use App\Action\GenerateRandomString;
use App\Enum\AssessmentParticipantStatus;
use App\Filament\Pages\MonitorAssessment;
use App\Models\Assessment;
use App\Models\AssessmentParticipant;
use App\Models\AssessmentToken;
use App\Models\User;
use Illuminate\Console\Command;

class trial extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trial';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        foreach (User::get() as $key => $user) {
            AssessmentParticipant::create([
                'user_id' => $user->id,
                'assessment_id' => Assessment::first()->id,
                'assessment_token_id' => AssessmentToken::first()->id,
                'start_time' => now()->toDateTimeString(),
                'status' => AssessmentParticipantStatus::ACTIVE,
                'point' => 0,
            ]);
        }
    }
}
