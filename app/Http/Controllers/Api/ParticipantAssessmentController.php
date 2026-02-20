<?php

namespace App\Http\Controllers\Api;

use App\Action\LockParticipant;
use App\Action\UnlockParticipant;
use App\Enum\ParticipantStatus;
use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Participant;
use App\Models\ParticipantAssessment;
use Exception;
use Illuminate\Http\Request;

class ParticipantAssessmentController extends Controller
{
    public function get($id)
    {
        try {
            $participantId = auth()->user()->id;
            $participant = ParticipantAssessment::query()
                            ->where([
                                'assessment_id' => $id,
                                'participant_id' => $participantId,
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
            $request->validate([
                'participant_assessment_id' => 'required',
            ]);

            $id = $request->participant_assessment_id;

            LockParticipant::execute($id);

            $pa = ParticipantAssessment::query()
                ->select([
                    'id',
                    'participant_id',
                    'assessment_id',
                    'start_time',
                    'end_time',
                    'status',
                ])
                ->where('id', $id)
                ->firstOrFail();

            $participantName = Participant::query()
                ->where('participants.id', $pa->participant_id)
                ->join('users', 'users.id', '=', 'participants.user_id')
                ->value('users.name');

            $assessment = Assessment::query()
                ->select(['name', 'time_test'])
                ->where('id', $pa->assessment_id)
                ->first();

            $response = [
                'id' => $pa->id,
                'participant_id' => $pa->participant_id,
                'assessment_id' => $pa->assessment_id,
                'start_time' => $pa->start_time,
                'end_time' => $pa->end_time,
                'participant_name' => $participantName,
                'assessment_name' => $assessment->name ?? null,
                'duration' => $assessment->time_test ?? null,
                'locked' => $pa->status === ParticipantStatus::LOCKED,
            ];

            return response()->json([
                'message' => 'SUKSES LOCK SISWA',
                'data' => $response,
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 400);
        }
    }

    public function unlock(Request $request)
    {
        try {
            $request->validate([
                'participant_assessment_id' => 'required',
                'unlock_token' => 'required',
            ]);

            $id = $request->participant_assessment_id;
            $unlockToken = $request->unlock_token;

            UnlockParticipant::execute($id, $unlockToken);

            $pa = ParticipantAssessment::query()
                ->select([
                    'id',
                    'participant_id',
                    'assessment_id',
                    'start_time',
                    'end_time',
                    'status',
                ])
                ->where('id', $id)
                ->firstOrFail();

            $participantName = Participant::query()
                ->where('participants.id', $pa->participant_id)
                ->join('users', 'users.id', '=', 'participants.user_id')
                ->value('users.name');

            $assessment = Assessment::query()
                ->select(['name', 'time_test'])
                ->where('id', $pa->assessment_id)
                ->first();

            $response = [
                'id' => $pa->id,
                'participant_id' => $pa->participant_id,
                'assessment_id' => $pa->assessment_id,
                'start_time' => $pa->start_time,
                'end_time' => $pa->end_time,
                'participant_name' => $participantName,
                'assessment_name' => $assessment->name ?? null,
                'duration' => $assessment->time_test ?? null,
                'locked' => $pa->status === ParticipantStatus::LOCKED,
            ];

            return response()->json([
                'message' => 'SUKSES LOCK SISWA',
                'data' => $response,
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 400);
        }
    }
}
