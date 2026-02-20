<?php

namespace App\Http\Controllers\Api;

use App\Action\RecalculateAssessmentPoint;
use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Assessment;
use App\Models\ParticipantAssessment;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function get(Request $request)
    {
        try {
            $request->validate([
                'assessment_id' => 'required',
            ]);
    
            $assessmentId = $request->assessment_id;
    
            $assessment = Assessment::findOrFail($assessmentId);
    
            $test = $assessment->test;
    
            $randomizeQuestion = $assessment->randomize_question;
            $randomizeAnswer = $assessment->randomize_answer;
    
            $testQuestions = $test->testQuestions;
            if ($randomizeQuestion) {
                $testQuestions = $testQuestions->shuffle();
            }
            
            $testQuestions = $testQuestions->map(function ($question) use ($randomizeAnswer) {
                $options = $randomizeAnswer
                    ? $question->options->shuffle()
                    : $question->options;
            
                $question->setRelation('options', $options);
            
                return $question;
            });
    
            return response()->json([
                'message' => 'SUKSES AMBIL DATA OPTIONS',
                'data' => $testQuestions,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 400);
        }
    }
    
    public function result($assessmentId)
    {
        $answer = Answer::query()
            ->whereHas('participantAssessment', fn($q) => $q->where([
                'participant_id' => auth()->user()->id,
                'assessment_id' => $assessmentId,
            ]))
            ->first();        

        if (!$answer) {
            return response()->json(
                [
                    "status" => false,
                    "message" => "Hasil belum tersedia atau sedang diproses",
                ],
                404,
            );
        }

        if ($answer->participantAssessment->point == 0) {
            RecalculateAssessmentPoint::execute($answer->participant_assessment_id);
        }

        $data = [
            'correct_answer' => $answer->correct_answers,
            'wrong_answer' => $answer->wrong_answers,
            'null_answer' => $answer->null_answers,
            'total_question' => $answer->participantAssessment->assessment->total_question,
            'final_point' => $answer->participantAssessment->point,
        ];

        return response()->json([
            "status" => true,
            "data" => $data,
        ]);
    }
}
