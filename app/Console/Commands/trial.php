<?php

namespace App\Console\Commands;

use App\Action\GenerateRandomString;
use App\Action\GenerateTestNumber;
use App\Action\PrintLoginCard;
use App\Action\RecalculateAssessmentPoint;
use App\Action\SyncParticipantAssessment;
use App\Enum\ParticipantStatus;
use App\Filament\Pages\MonitorAssessment;
use App\Models\Answer;
use App\Models\Assessment;
use App\Models\Participant;
use App\Models\AssessmentToken;
use App\Models\Module;
use App\Models\ParticipantGroup;
use App\Models\Test;
use App\Models\TestQuestion;
use App\Models\TestQuestionOption;
use App\Models\Topic;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

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
        RecalculateAssessmentPoint::execute('019eeaef-1dbe-7170-bcee-cf53e7d3e0c6');
    }
}
