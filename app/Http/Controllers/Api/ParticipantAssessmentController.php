<?php

namespace App\Http\Controllers\Api;

use App\Action\LockParticipant;
use App\Action\UnlockParticipant;
use App\Http\Controllers\Controller;
use App\Models\Participant;
use App\Models\ParticipantAssessment;
use Exception;
use Illuminate\Http\Request;

class ParticipantAssessmentController extends Controller
{
    public function get(Request $request)
    {
        try {
            $request->validate([
                'assessment_id' => 'required',
                'participant_id' => 'required',
            ]);
            $participant = ParticipantAssessment::query()
                            ->where([
                                'assessment_id' => $request->assessment_id,
                                'participant_id' => $request->participant_id,
                            ])
                            ->first();
            
            if (!$participant) {
                throw new Exception("TIDAK KETEMU SISWA PADA UJIAN!");
            }

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
            $participantAssessmentId = $request->participant_assessment_id;
            LockParticipant::execute($participantAssessmentId);

            return response()->json([
                'message' => "SUKSES LOCK SISWA",
                'data' => ParticipantAssessment::findOrFail($participantAssessmentId)

            ]);
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
            'participant_assessment_id' => 'required',
            'unlock_token' => 'required',
        ]);

        try {
            $participantAssessmentId = $request->participant_assessment_id;
            $unlockToken = $request->unlock_token;

            UnlockParticipant::execute($participantAssessmentId, $unlockToken);

            return response()->json([
                'message' => "SUKSES UNLOCK USER",
                'data' => ParticipantAssessment::findOrFail($participantAssessmentId)
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ],
            400);
        }
    }
}
