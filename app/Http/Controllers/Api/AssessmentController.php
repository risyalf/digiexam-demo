<?php

namespace App\Http\Controllers\Api;

use App\Enum\ParticipantStatus;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessAnswer;
use App\Models\Answer;
use App\Models\Assessment;
use App\Models\Participant;
use App\Models\AssessmentToken;
use App\Models\TestQuestion;
use App\Models\TestQuestionOption;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function Symfony\Component\Clock\now;

class AssessmentController extends Controller
{
    public function get() {}

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
        $request->validate([
            "assessment_id" => "required",
            "assessment_token" => "required",
        ]);

        try {
            $assessmentId = Assessment::first()->id;
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

            $participant = Participant::query()
                ->where("status", "!=", ParticipantStatus::FINISH)
                ->where([
                    "user_id" => auth()->user()->id,
                ])
                ->first();

            if (!$participant) {
                $participant = Participant::create([
                    "user_id" => auth()->user()->id,
                    "assessment_id" => $assessmentId,
                    "assessment_token_id" => $token->id,
                    "start_time" => Carbon::now()->toDateTimeString(),
                    "end_time" => Carbon::now()->addMinutes(
                        $assessment->time_test,
                    ),
                    "status" => ParticipantStatus::ACTIVE,
                ]);
            } else {
                $participant->update([
                    "assessment_id" => $assessmentId,
                    "assessment_token_id" => $token->id,
                    "start_time" => Carbon::now()->toDateTimeString(),
                    "end_time" => Carbon::now()->addMinutes(
                        $assessment->time_test,
                    ),
                    "status" => ParticipantStatus::ACTIVE,
                ]);
            }

            return response([
                "message" => "SUKSES MASUK KE UJIAN",
                "data" => $participant,
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
