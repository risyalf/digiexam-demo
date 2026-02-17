<?php

namespace App\Http\Controllers\Api;

use App\Enum\ParticipantStatus;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessAnswer;
use App\Models\Answer;
use App\Models\Assessment;
use App\Models\Participant;
use App\Models\AssessmentToken;
use App\Models\ParticipantAssessment;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssessmentController extends Controller
{
    public function get() 
    {
        try {
            $participantId = auth()->user()->id;
            $assessments = Assessment::query()
                                ->join('participant_assessments as pa', 'assessments.id', 'pa.assessment_id')
                                ->select([
                                    'assessments.*',
                                    'pa.id as participant_assessment_id'
                                ])
                                ->where('pa.participant_id', $participantId)
                                ->get();

            return response([
                "message" => "SUKSES MENGAMBIL DATA ASSESSMENT",
                "data" => $assessments
            ]);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    "message" => $th->getMessage(),
                ],
                400,
            );
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            "updates" => "required",
        ]);

        try {
            DB::beginTransaction();

            $id = auth()->user()->id;
            $updates = $request->updates;

            Participant::where("user_id", $id)->update($updates);

            DB::commit();

            return response([
                "message" => "SUKSES UPDATE DATA PARTISIPAN",
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(
                [
                    "message" => $th->getMessage(),
                ],
                400,
            );
        }
    }

    public function join(Request $request)
    {
        try {
            $request->validate([
                "assessment_id" => "required",
                "assessment_token" => "required",
            ]);

            $assessmentId = Assessment::first()->id;
            $participantId = auth()->user()->id;
            $assessmentToken = $request->assessment_token;

            $token = AssessmentToken::query()
                ->where("value", $assessmentToken)
                ->first();

            if (!$token) {
                throw new Exception("TOKEN TIDAK DI TEMUKAN!");
            }

            if (Carbon::now()->isAfter($token->expired_until)) {
                throw new Exception(
                    "TOKEN TELAH EXPIRED. SILAHKAN MINTA TOKEN BARU KE OPERATOR.",
                );
            }

            if (!$token->allModule && $token->assessment_id != $assessmentId) {
                throw new Exception("TOKEN TIDAK UNTUK UJIAN INI!");
            }

            $assessment = Assessment::find($assessmentId);
            if (!$assessment) {
                throw new Exception("UJIAN TIDAK KETEMU DI DATABASE!");
            }

            $participantAssessment = ParticipantAssessment::query()
                ->where([
                    "participant_id" => $participantId,
                    "assessment_id" => $assessmentId
                ])
                ->first();

            if (!$participantAssessment) {
                throw new Exception("DATA SISWA UNTUK MENGIKUTI UJIAN TIDAK DI TEMUKAN!");
            }
            if ($participantAssessment->status == ParticipantStatus::FINISH) {
                throw new Exception("SISWA TELAH SELESAI MENGERJAKAN UJIAN!");
            }

            $participantAssessment->update([
                "assessment_token_id" => $token->id,
                "status" => ParticipantStatus::LOGGED_IN,
                "last_status" => $participantAssessment->status
            ]);

            $data = $participantAssessment->only([
                'id',
                'assessment_id',
                'start_time',
                'end_time'
            ]);

            $data['participant_name'] = $participantAssessment->participant->user->name;
            $data['assessment_name'] = $participantAssessment->assessment->name;
            $data['duration'] = $participantAssessment->assessment->time_test;

            return response([
                "message" => "SUKSES MASUK KE UJIAN",
                "data" => $data,
            ]);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    "message" => $th->getMessage(),
                ],
                400,
            );
        }
    }

    public function start(Request $request)
    {
        try {
            $request->validate([
                "participant_assessment_id" => "required",
            ]);
            
            $participantAssessmentId = $request->participant_assessment_id;
            $participantAssessment = ParticipantAssessment::findOrFail($participantAssessmentId);
            $assessment = $participantAssessment->assessment;

            $participantAssessment->update([
                "start_time" => Carbon::now()->toDateTimeString(),
                "end_time" => Carbon::now()->addMinutes(
                    $assessment->time_test,
                ),
                "status" => ParticipantStatus::IN_PROGRESS,
                "last_status" => $participantAssessment->status
            ]);

            $data = $participantAssessment->only([
                'id',
                'assessment_id',
                'start_time',
                'end_time'
            ]);

            $data['participant_name'] = $participantAssessment->participant->user->name;
            $data['assessment_name'] = $participantAssessment->assessment->name;
            $data['duration'] = $participantAssessment->assessment->time_test;

            return response([
                "message" => "SUKSES MULAI UJIAN",
                "data" => $data,
            ]);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    "message" => $th->getMessage(),
                ],
                400,
            );
        }
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            "assessment_id" => "required|uuid",
            "participant_id" => "required|uuid",
            "topic_id" => "required|uuid",
            "test_id" => "required|uuid",
            "value" => "required|array|min:1",
            "value.*.test_question_id" => "required|uuid",
            "value.*.answer" => "nullable|uuid",
        ]);

        ProcessAnswer::dispatch($validated);

        return response()->json([
            "message" => "Jawaban sedang diproses",
        ]);
    }

    public function result($assessmentId, $participantId)
    {
        $result = Answer::query()
            ->where("assessment_id", $assessmentId)
            ->where("participant_id", $participantId)
            ->first();

        if (!$result) {
            return response()->json(
                [
                    "status" => false,
                    "message" => "Hasil belum tersedia atau sedang diproses",
                ],
                404,
            );
        }

        return response()->json([
            "status" => true,
            "data" => $result,
        ]);
    }
}
