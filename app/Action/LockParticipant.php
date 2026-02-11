<?php

namespace App\Action;

use App\Enum\ParticipantStatus;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LockParticipant
{
    public static function execute($id)
    {
        return Participant::query()
            ->where([
                'id' => $id,
            ])
            ->where('status', '!=', ParticipantStatus::FINISH)
            ->update([
                'status' => ParticipantStatus::LOCKED
            ]);
    }
}
