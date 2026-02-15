<?php

namespace App\Action;

use App\Models\AssessmentParticipantGroup;
use App\Models\Participant;
use App\Models\ParticipantAssessment;

class SyncParticipantAssessment
{
    public static function execute(string $assessmentId): void
    {
        $groupIds = AssessmentParticipantGroup::query()
                            ->where('assessment_id', $assessmentId)
                            ->pluck('participant_group_id')
                            ->toArray();

        $participantIds = Participant::query()
                            ->whereIn('participant_group_id', $groupIds)
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
