<?php

namespace App\Console\Commands;

use App\Action\GenerateRandomString;
use App\Action\GenerateTestNumber;
use App\Action\PrintLoginCard;
use App\Action\SyncParticipantAssessment;
use App\Enum\ParticipantStatus;
use App\Filament\Pages\MonitorAssessment;
use App\Models\Assessment;
use App\Models\Participant;
use App\Models\AssessmentToken;
use App\Models\Test;
use App\Models\User;
use Carbon\Carbon;
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
        SyncParticipantAssessment::execute('019c78d2-8f3c-7319-9ec9-a6c4063c4ea2');
    }
}
