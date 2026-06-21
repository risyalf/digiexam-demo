<?php

namespace App\Action;

use App\Enum\AssessmentType;
use App\Models\ParticipantAssessment;

class RecalculateAssessmentPoint
{
    public static function execute(string $participantAssessmentId): ParticipantAssessment
    {
        $participantAssessment = $participantAssessment = ParticipantAssessment::with([
            'answer',
            'assessment',
            'assessment.test',
            'assessment.test.testQuestions',
        ])->findOrFail($participantAssessmentId);
        $answer = $participantAssessment->answer;
        $assessment = $participantAssessment->assessment;
        $questions = $assessment->test->testQuestions;

        $maxMultipleAnswerValue = $questions->where("type", AssessmentType::PILIHAN_GANDA->value)->count();
        $essayCount = $questions->where("type", AssessmentType::ESAI->value)->count();
        $maxEssayValue = $essayCount * $assessment->max_essay_point;
        $maxPoint = $maxMultipleAnswerValue + $maxEssayValue;

        $point = 0;

        $essayValue = $answer->essay_values;
        $jsonAnswer = json_decode($essayValue);

        if (count($jsonAnswer) > 0) {
            $pointEssay = collect($jsonAnswer)->where('evaluated', true)->sum('point');
            $point += $pointEssay;
        }

        $point += $answer->correct_answers;
        if ($maxPoint > 0) {
            $point = ($point / $maxPoint) * 100;
        }

        $participantAssessment->point = $point;
        $participantAssessment->save();

        return $participantAssessment;
    }
}
