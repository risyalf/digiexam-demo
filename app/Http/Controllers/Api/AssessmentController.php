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
use App\Models\TestQuestion;
use App\Models\TestQuestionOption;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssessmentController extends Controller
{
    public function find($id)
    {
        try {
            $assessment = Assessment::query()
                            ->where('id', $id)
                            ->select([
                                'id',
                                'name',
                                'start_date',
                                'end_date',
                                'time_test' 
                            ])
                            ->first();

            $participantAssessment = ParticipantAssessment::query()
                                        ->where([
                                            'participant_id' => auth()->user()->id,
                                            'assessment_id' => $id
                                        ])
                                        ->first();

            $data = [
                'id' => $assessment->id,
                'participant_assessment_id' => $participantAssessment->id,
                'name' => $assessment->name,
                'start_date' => $assessment->start_date,
                'end_date' => $assessment->end_date,
                'time_test' => $assessment->time_test,
                'status' => $participantAssessment->status,
            ];

            return response([
                "message" => "SUKSES MENGAMBIL DATA ASSESSMENT",
                "data" => $data
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

    public function get()
    {
        try {
            $participantId = auth()->user()->id;
            $participantAssessments = ParticipantAssessment::query()
                ->where('participant_id', $participantId)
                ->get();

            $data = [];

            foreach ($participantAssessments as $key => $participantAssessment) {
                $assessment = Assessment::query()
                    ->select([
                        'id',
                        'name',
                        'start_date',
                        'end_date',
                        'time_test'
                    ])
                    ->where('id', $participantAssessment->assessment_id)
                    ->first();

                $data[] = [
                    'id' => $assessment->id,
                    'participant_assessment_id' => $participantAssessment->id,
                    'name' => $assessment->name,
                    'start_date' => $assessment->start_date,
                    'end_date' => $assessment->end_date,
                    'time_test' => $assessment->time_test,
                    'status' => $participantAssessment->status,
                ];
            }

            return response([
                "message" => "SUKSES MENGAMBIL DATA ASSESSMENT",
                "data" => $data
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
            $data['locked'] = $participantAssessment->status == ParticipantStatus::LOCKED;

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
            $data['locked'] = $participantAssessment->status == ParticipantStatus::LOCKED;

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
            "participant_assessment_id" => "required|uuid",
            "value" => "required|array|min:1",
            "value.*.test_question_id" => "required|uuid",
            "value.*.answer" => "nullable|int",
        ]);

        ParticipantAssessment::query()
            ->where('id', $request->participant_assessment_id)
            ->update([
                'status' => ParticipantStatus::FINISH,
                'last_status' => ParticipantStatus::IN_PROGRESS,
            ]);

        // ProcessAnswer::dispatch($validated);

        $answers = collect($validated["value"]);

        $questionIds = $answers->pluck("test_question_id")->unique();

        $participantAssessment = ParticipantAssessment::findOrFail($validated['participant_assessment_id']);
        $testId = $participantAssessment->assessment->test->id;
        $validQuestionIds = TestQuestion::query()
            ->where("test_id", $testId)
            ->whereIn("id", $questionIds)
            ->pluck("id")
            ->toArray();

        $correctOptions = TestQuestionOption::query()
            ->whereIn("test_question_id", $validQuestionIds)
            ->where("value", true)
            ->get()
            ->keyBy("test_question_id");

        $correct = 0;
        $wrong = 0;

        foreach ($answers as $item) {
            if (!in_array($item["test_question_id"], $validQuestionIds)) {
                continue;
            }

            $answer = $item["answer"];

            $correctOption = $correctOptions[$item["test_question_id"]] ?? null;

            if ($correctOption && $correctOption->id == $answer) {
                $correct++;
            } else {
                $wrong++;
            }
        }

        $jsonValue = json_encode($validated['value']);

        DB::transaction(function () use ($correct, $wrong, $participantAssessment, $jsonValue) {
            $totalQuestion = $participantAssessment->assessment->total_question;    
            $null = $totalQuestion - ($correct + $wrong);

            Answer::updateOrCreate(
                [
                    "participant_assessment_id" => $participantAssessment->id,
                ],
                [
                    "correct_answers" => $correct,
                    "wrong_answers" => $wrong,
                    "null_answers" => $null,
                    "value" => $jsonValue,
                ],
            );

            $point = $correct / $totalQuestion * 100;

            $participantAssessment->status = ParticipantStatus::SUBMITTED;
            $participantAssessment->last_status = $participantAssessment->status;
            $participantAssessment->point = $point;
            $participantAssessment->save();
        });

        return response()->json([
            "message" => "Jawaban sedang diproses",
        ]);
    }
}
