<?php

namespace App\Console\Commands;

use App\Enum\ParticipantStatus;
use App\Models\Answer;
use App\Models\ApiLog;
use App\Models\ParticipantAssessment;
use App\Models\TestQuestion;
use App\Models\TestQuestionOption;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateAnswerFromApiLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-answer-from-api-log';

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
        $assessmentIds = ParticipantAssessment::query()
            ->select('participant_assessments.id')
            ->join('participants', 'participants.id', '=', 'participant_assessments.participant_id')
            ->where('participant_assessments.status', ParticipantStatus::SUBMITTED)
            ->doesntHave('answers')
            ->pluck('id');

        foreach ($assessmentIds as $key => $id) {
            $log = ApiLog::query()
                ->where('json', 'like', "%$id%")
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$log) {
                $this->error("NO API LOG FOUND FOR ASSESSMENT $id");
                continue;
            }

            $str = $log->json;
            $json = json_decode($str, true);
            $participantAssessmentId = $json['participant_assessment_id'];
            $value = $json['value'];

            $answers = collect($value);

            $questionIds = $answers->pluck("test_question_id")->unique();

            $participantAssessment = ParticipantAssessment::findOrFail($participantAssessmentId);
            $assessment = $participantAssessment->assessment;
            $testId = $assessment->test_id;
            $validQuestions = TestQuestion::query()
                ->where("test_id", $testId)
                ->whereIn("id", $questionIds)
                ->select([
                    "id",
                    "type"
                ])->get();

            $validQuestionIds = $validQuestions->pluck('id')->toArray();

            $correctOptions = TestQuestionOption::query()
                ->whereIn("test_question_id", $validQuestionIds)
                ->where("value", true)
                ->get()
                ->keyBy("test_question_id");

            $correct = 0;
            $wrong = 0;
            $essayAnswers = [];

            foreach ($answers as $item) {
                if (!in_array($item["test_question_id"], $validQuestionIds)) {
                    continue;
                }

                $answer = $item["answer"];

                if ($validQuestions->where('id', $item["test_question_id"])->first()->type == 'Esai') {
                    $essayAnswers[] = [
                        "test_question_id" => $item['test_question_id'],
                        "value" => $answer,
                        "evaluated" => false,
                        "point" => 0
                    ];
                    continue;
                }

                $correctOption = $correctOptions[$item["test_question_id"]] ?? null;

                if ($correctOption && $correctOption->id == $answer) {
                    $correct++;
                } else {
                    $wrong++;
                }
            }

            $jsonValue = json_encode($value);
            $essayValue = json_encode($essayAnswers);
            $containEssay = count($essayAnswers) > 0;
            DB::transaction(function () use ($correct, $wrong, $participantAssessment, $assessment, $jsonValue, $essayValue, $containEssay) {
                $totalQuestion = $assessment->total_question;
                $null = $totalQuestion - ($correct + $wrong);

                Answer::updateOrCreate(
                    [
                        "participant_assessment_id" => $participantAssessment->id,
                    ],
                    [
                        "correct_answers" => $correct,
                        "wrong_answers" => $wrong,
                        "null_answers" => $null,
                        "value" => $jsonValue,
                        "essay_values" => $essayValue,
                    ],
                );

                $point = 0;

                if (!$containEssay) {
                    $point = $correct / $totalQuestion * 100;
                }

                $participantAssessment->point = $point;
                $participantAssessment->save();
            });
        }
    }
}
