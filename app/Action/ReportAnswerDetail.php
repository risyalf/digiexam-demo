<?php

namespace App\Action;

use App\Exports\ManualExport;
use App\Models\Answer;
use App\Models\Assessment;
use App\Models\Module;
use App\Models\ParticipantGroup;
use App\Models\Topic;
use Maatwebsite\Excel\Facades\Excel;

class ReportAnswerDetail
{
    public static function execute(array $data)
    {
        $moduleId = $data['module_id'];
        $topicId = $data['topic_id'];
        $groupId = $data['group_id'];

        $module = Module::findOrFail($moduleId);
        $topic = Topic::findOrFail($topicId);
        $group = $groupId ? ParticipantGroup::findOrFail($groupId) : null;

        $name = self::buildFileName($module->name, $topic->name, $group?->name);

        $answers = Answer::query()
                    ->with('participantAssessment.participant.user', 'participantAssessment.participant.participantGroup')
                    ->whereHas('participantAssessment.assessment', function ($q) use($moduleId, $topicId, $groupId) {
                        $q->where([
                            'module_id' => $moduleId,
                            'topic_id' => $topicId
                        ])
                        ->when($groupId, fn($q) => $q->whereRaw(
                            "exists(
                                select 1 from assessment_participant_groups apg
                                where apg.assessment_id = id
                                and apg.participant_group_id = '$groupId'
                            )"
                        ));
                    })
                    ->get();

        $assessment = Assessment::query()
                        ->where([
                            'module_id' => $moduleId,
                            'topic_id' => $topicId
                        ])
                        ->when($groupId, fn($q) => $q->whereRaw(
                            "exists(
                                select 1 from assessment_participant_groups apg
                                where apg.assessment_id = id
                                and apg.participant_group_id = '$groupId'
                            )"
                        ))
                        ->firstOrFail();

        $test = $assessment->test;
        $questions = $test->testQuestions()->with('options')->orderBy('ordering')->get();
        $questionTexts = $questions
                            ->pluck('name')
                            ->map(fn ($text) => self::sanitizeQuestionText($text))
                            ->toArray();

        $headers = [
            '',
            '',
            '',
            'Soal',
            '',
            '',
            ...$questionTexts
        ];

        $correctAnswers = $questions->map(function ($question) {
            return $question->options->firstWhere('value', true)?->content ?? '';
        })->toArray();

        $optionContentById = $questions
            ->flatMap(fn ($question) => $question->options)
            ->pluck('content', 'id');

        $rows = [
            $headers,
            [
                'Nama',
                'Kelas',
                'Modul',
                'Topic',
                'Nilai',
                'Waktu Submit',
                ...$correctAnswers
            ],
        ];

        foreach ($answers as $answer) {
            $participantAssessment = $answer->participantAssessment;
            $participant = $participantAssessment->participant;
            $decodedAnswers = collect(json_decode($answer->value) ?? [])->keyBy('test_question_id');
            $answerArray = [];

            foreach ($questions as $question) {
                $currentAnswer = $decodedAnswers->get($question->id);
                $answerArray[] = $currentAnswer
                    ? ($optionContentById[$currentAnswer->answer] ?? '')
                    : '';
            }

            $rows[] = [
                $participant->user->name,
                $participant->participantGroup?->name ?? '',
                $module->name,
                $topic->name,
                $participantAssessment->point,
                $answer->created_at->toDateTimeString(),
                ...$answerArray
            ];
        }

        return Excel::download(new ManualExport([], $rows, [1, 2]), $name);
    }

    private static function buildFileName(string $moduleName, string $topicName, ?string $groupName): string
    {
        $name = "Report_Detail.$moduleName.$topicName";
        $name = $groupName ? "$name.$groupName" : $name;

        return "$name.xlsx";
    }

    private static function sanitizeQuestionText(string $text): string
    {
        $clean = trim(html_entity_decode(strip_tags($text)));

        return preg_replace('/\s+/', ' ', $clean) ?? $clean;
    }
}
