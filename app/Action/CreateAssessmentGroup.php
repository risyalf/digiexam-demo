<?php

namespace App\Action;

use App\Models\AssessmentParticipantGroup;

class CreateAssessmentGroup
{
    public static function execute(array $participantGroupIds, string $assessmentId): void
    {
        $datas = [];
        foreach ($participantGroupIds as $key => $participantGroupId) {
            $datas[] = [
                'assessment_id' => $assessmentId,
                'participant_group_id' => $participantGroupId,
            ];
        }
        // AssessmentParticipantGroup::insert($datas);
    }
}
