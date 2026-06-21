<?php

namespace App\Jobs;

use App\Enum\ParticipantStatus;
use App\Models\Answer;
use App\Models\ParticipantAssessment;
use App\Models\TestQuestion;
use App\Models\TestQuestionOption;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class ProcessAnswer implements ShouldQueue
{
    use Queueable;

    protected array $validated;

    /**
     * Create a new job instance.
     */
    public function __construct(array $validated)
    {
        $this->validated = $validated;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $answers = collect($this->validated["value"]);

        $questionIds = $answers->pluck("test_question_id")->unique();

        $participantAssessment = ParticipantAssessment::findOrFail($this->validated['participant_assessment_id']);
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

        $jsonValue = json_encode($this->validated['value']);
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
