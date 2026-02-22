<?php

namespace App\Action;

use App\Exports\ManualExport;
use App\Models\Answer;
use App\Models\Module;
use App\Models\ParticipantGroup;
use App\Models\Topic;
use Maatwebsite\Excel\Facades\Excel;

class ReportAnswerSummary
{
    public static function execute(array $data)
    {
        $moduleId = $data['module_id'];
        $topicId = $data['topic_id'];
        $groupId = $data['group_id'];
        $createdAt = $data['created_at'];

        $module = Module::findOrFail($moduleId);
        $topic = Topic::findOrFail($topicId);
        $group = $groupId ? ParticipantGroup::findOrFail($groupId) : null;

        $name = "Report_Summary.$module->name.$topic->name";
        $name = $group ? $name.".$group->name" : $name;
        $name = $name.".xlsx";

        $answers = Answer::query()
                    ->with('participantAssessment')
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
                    ->when($createdAt, fn($q) => $q->whereDate('created_at', $createdAt))
                    ->get();

        $headers = [
            'Nama',
            'Kelas',
            'Modul',
            'Topic',
            'Nilai',
        ];

        $rows = [];

        foreach ($answers as $answer) {
            $participantAssessment = $answer->participantAssessment;
            $participant = $participantAssessment->participant;
            $rows[] = [
                $participant->user->name,
                $participant->participantGroup->name,
                $module->name,
                $topic->name,
                $participantAssessment->point
            ];
        }

        return Excel::download(new ManualExport($headers, $rows, [1]), $name);
    }
}
