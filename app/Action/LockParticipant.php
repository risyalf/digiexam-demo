<?php

namespace App\Action;

use App\Enum\ParticipantStatus;
use App\Models\ParticipantAssessment;

class LockParticipant
{
    public static function execute($id)
    {
        $participantAssessment = ParticipantAssessment::findOrFail($id);
        if (in_array($participantAssessment->status, [
            ParticipantStatus::FINISH,
            ParticipantStatus::LOCKED
        ])) {
            return;
        }

        $lastStatus = $participantAssessment->status;
        $participantAssessment->unlock_token = null;
        $participantAssessment->status = ParticipantStatus::LOCKED;
        $participantAssessment->last_status = $lastStatus;
        $participantAssessment->save();
    }
}
