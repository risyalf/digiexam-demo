<?php

namespace App\Action;

use App\Enum\AssessmentParticipantStatus;
use App\Models\AssessmentParticipant;
use App\Models\User;

class LockUser
{
    public static function execute($userId)
    {
        AssessmentParticipant::query()
            ->where([
                'user_id' => $userId,
            ])
            ->where('status', '!=', AssessmentParticipantStatus::FINISH)
            ->update([
                'status' => AssessmentParticipantStatus::LOCKED
            ]);

        return User::where('id', $userId)
                ->update([
                    'is_locked' => true,
                    'unlock_token' => null
                ]);
    }
}
