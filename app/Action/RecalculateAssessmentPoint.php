<?php

namespace App\Action;

use App\Models\ParticipantAssessment;

class RecalculateAssessmentPoint
{
    public static function execute(string $participantAssessmentId): ParticipantAssessment
    {
        $participantAssessment = ParticipantAssessment::findOrFail($participantAssessmentId);
        $answer = $participantAssessment->answer;

        $totalQuestion = $participantAssessment->assessment->total_question;

        $point = (float)0;
        $point = $answer->correct_answers / $totalQuestion * 100;

        $participantAssessment->point = $point;
        $participantAssessment->save();

        return $participantAssessment;
    }
}
