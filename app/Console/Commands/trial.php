<?php

namespace App\Console\Commands;

use App\Action\GenerateRandomString;
use App\Action\GenerateTestNumber;
use App\Action\PrintLoginCard;
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
        $data = [
            'module_id' => '019c7b90-4269-71b3-8308-a7d7626f7373',
            'topic_id' => '019c7b90-6215-73a3-b9b7-ee6ef4830b53',
            'group_id' => null
        ];
        $moduleId = $data['module_id'];
        $topicId = $data['topic_id'];
        $groupId = $data['group_id'];

        $module = Module::findOrFail($moduleId);
        $topic = Topic::findOrFail($topicId);
        $group = $groupId ? ParticipantGroup::findOrFail($groupId) : null;

        $name = "Report_Jawaban_$module->name.$topic->name";
        $name = $group ? $name.".$group->name" : $name;
        $name = $name.".xlsx";

        $answers = Answer::query()
                    ->with('participantAssessment')
                    ->whereHas('participantAssessment.assessment', function ($q) use($moduleId, $topicId, $groupId) {
                        $q->where([
                            'module_id' => $moduleId,
                            'topic_id' => $topicId
                        ])
                        ->when($groupId, fn($q) => $q->where('group_id', $groupId));
                    })
                    ->get();

        $assessment = Assessment::query()
                        ->where([
                            'module_id' => $moduleId,
                            'topic_id' => $topicId
                        ])
                        ->when($groupId, fn($q) => $q->where('group_id', $groupId))
                        ->first();

        $test = $assessment->test;
        $questions = $test->testQuestions()->orderBy('ordering')->get();
        $questionTexts = $questions->pluck('name')->toArray();

        $headers = [
            '',
            '',
            '',
            'Soal',
            '',
            '',
            ...$questionTexts
        ];

        $correctAnswers = $questions->map(function($question) {
            return $question->options->where(fn($option) => $option->value == true)->first();
        })
        ->pluck('content')
        ->toArray();

        $rows = [];

        $rows = [
            'Nama',
            'Kelas',
            'Modul',
            'Topic',
            'Nilai',
            'Waktu Submit',
            ...$correctAnswers
        ];

        foreach ($answers as $answer) {
            $participantAssessment = $answer->participantAssessment;
            $participant = $participantAssessment->participant;
            $collectionAnswers = collect(json_decode($answer->value));
            $answers = [];
            foreach ($questions as $question) {
                $answer = $collectionAnswers->where(fn($data) => $data->test_question_id == $question->id)->first();
                if ($answer) {
                    $answers[] = TestQuestionOption::find($answer->answer)->content;
                    continue;
                }

                $answers[] = '';
            }
            $rows[] = [
                $participant->user->name,
                $participant->participantGroup->name,
                $module->name,
                $topic->name,
                $participantAssessment->point,
                $answer->created_at,
                ...$answers
            ];
        }
    }
}
