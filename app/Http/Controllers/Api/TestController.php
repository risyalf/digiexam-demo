<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
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
}
