<?php

namespace App\Action;

use App\Models\AssessmentToken;
use App\Models\Participant;
use App\Models\ParticipantAssessment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CreateAssessmentParticipant
{
    public static function execute(array $participantGroupIds, string $assessmentId): void
    {
        $participantIds = Participant::query()
                        ->whereIn('group_id', $participantGroupIds)
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
            ->upsert([
                $data
            ], ['participant_id', 'assessment_id']);
    }
}
