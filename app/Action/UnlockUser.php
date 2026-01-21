<?php

namespace App\Action;

use App\Enum\AssessmentParticipantStatus;
use App\Models\AssessmentParticipant;
use App\Models\User;
use Exception;

class UnlockUser
{
    public static function execute($id, $token)
    {
        $user = User::findOrFail($id);

        if (!$user->is_locked) {
            throw new Exception("USER TIDAK DALAM KONDISI TERKUNCI.");
        }

        if ($user->unlock_token != $token) {
            throw new Exception("TOKEN SALAH!");
        }

        AssessmentParticipant::query()
            ->where([
                'user_id' => $user->id,
            ])
            ->where('status', '!=', AssessmentParticipantStatus::FINISH)
            ->update([
                'status' => AssessmentParticipantStatus::ACTIVE
            ]);

        return $user->update([
            'is_locked' => false,
            'unlock_token' => null
        ]);
    }
}
