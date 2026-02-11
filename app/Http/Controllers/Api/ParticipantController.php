<?php

namespace App\Http\Controllers\Api;

use App\Action\LockParticipant;
use App\Action\UnlockParticipant;
use App\Http\Controllers\Controller;
use App\Models\Participant;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    public function get($id)
    {
        try {
            $participant = Participant::findOrFail($id);

            return response()->json([
                'message' => "SUKSES GET DATA SISWA",
                'data' => $participant
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ],
            400);
        }
    }

    public function lock(Request $request)
    {
        try {
            $participantId = $request->participant_id;
            LockParticipant::execute($participantId);

            return response()->json(['message' => "SUKSES LOCK SISWA"]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ],
            400);
        }
    }

    public function unlock(Request $request)
    {
        $request->validate([
            'participant_id' => 'required',
            'unlock_token' => 'required',
        ]);

        try {
            $participantId = $request->participant_id;
            $unlockToken = $request->unlock_token;

            UnlockParticipant::execute($participantId, $unlockToken);

            return response()->json(['message' => "SUKSES UNLOCK USER"]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ],
            400);
        }
    }
}
