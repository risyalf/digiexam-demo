<?php

namespace App\Action;

use App\Enum\ParticipantStatus;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LockUser
{
    public static function execute($userId)
    {
        Participant::query()
            ->where([
                'user_id' => $userId,
            ])
            ->where('status', '!=', ParticipantStatus::FINISH)
            ->update([
                'status' => ParticipantStatus::LOCKED
            ]);

        return User::where('id', $userId)
                ->update([
                    'is_locked' => DB::raw('true'),
                    'unlock_token' => null
                ]);
    }
}
