<?php

namespace App\Action;

use App\Models\Assessment;
use App\Models\AssessmentParticipantGroup;
use App\Models\Participant;
use App\Models\ParticipantAssessment;

class SyncParticipantAssessment
{
    public static function execute(string $assessmentId): void
    {
        $assessment = Assessment::find($assessmentId);
        $groupIds = AssessmentParticipantGroup::query()
                            ->where('assessment_id', $assessmentId)
                            ->pluck('participant_group_id')
                            ->toArray();

        $participantIds = Participant::query()
                            ->whereIn('participant_group_id', $groupIds)
                            ->where('module_id', $assessment->module_id)
                            ->pluck('id')
                            ->toArray();

        $data = [];

        foreach ($participantIds as $key => $participantId) {
            $data[] = [
                'participant_id' => $participantId,
                'assessment_id' => $assessmentId,
            ];
        }

        ParticipantAssessment::query()
            ->upsert($data, ['participant_id', 'assessment_id']);
    }
}
