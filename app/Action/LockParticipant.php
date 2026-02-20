<?php

namespace App\Action;

use App\Enum\ParticipantStatus;
use App\Models\ParticipantAssessment;

class LockParticipant
{
    public static function execute($id)
    {
        return ParticipantAssessment::query()
            ->where([
                'id' => $id,
            ])
            ->where('status', '!=', ParticipantStatus::FINISH)
            ->update([
                'unlock_token' => null,
                'status' => ParticipantStatus::LOCKED
            ]);
    }
}
