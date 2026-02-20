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
        $testId = $participantAssessment->assessment->test->id;
        $validQuestionIds = TestQuestion::query()
            ->where("test_id", $testId)
            ->whereIn("id", $questionIds)
            ->pluck("id")
            ->toArray();

        $correctOptions = TestQuestionOption::query()
            ->whereIn("test_question_id", $validQuestionIds)
            ->where("value", true)
            ->get()
            ->keyBy("test_question_id");

        $correct = 0;
        $wrong = 0;
        $null = 0;

        foreach ($answers as $item) {
            if (!in_array($item["test_question_id"], $validQuestionIds)) {
                continue;
            }

            if (!$item["answer"]) {
                $null++;
                continue;
            }

            $correctOption = $correctOptions[$item["test_question_id"]] ?? null;

            if ($correctOption && $correctOption->id === $item["answer"]) {
                $correct++;
            } else {
                $wrong++;
            }
        }

        DB::transaction(function () use ($correct, $wrong, $null, $participantAssessment) {
            Answer::updateOrCreate(
                [
                    "participant_assessment_id" => $participantAssessment->id,
                ],
                [
                    "correct_answers" => $correct,
                    "wrong_answers" => $wrong,
                    "null_answers" => $null,
                    "value" => $validated["value"],
                ],
            );
        });

        $participantAssessment->status = ParticipantStatus::SUBMITTED;
        $participantAssessment->last_status = $participantAssessment->status;
        $participantAssessment->save();
    }
}
