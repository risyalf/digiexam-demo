<?php

namespace App\Action;

use App\Enum\ParticipantStatus;
use App\Models\Participant;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

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

        Participant::query()
            ->where([
                'user_id' => $user->id,
            ])
            ->where('status', '!=', ParticipantStatus::FINISH)
            ->update([
                'status' => ParticipantStatus::ACTIVE
            ]);

        return $user->update([
            'is_locked' => DB::raw('false'),
            'unlock_token' => null
        ]);
    }
}
