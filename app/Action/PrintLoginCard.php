<?php

namespace App\Action;

use App\Models\Participant;
use App\Models\ParticipantAssessment;
use Carbon\Carbon;

use function Symfony\Component\Clock\now;

class PrintLoginCard
{
    public static function execute(string $module_id, string $groupId)
    {
        $participants = Participant::query()
                            ->where('module_id', $module_id)
                            ->where('participant_group_id', $groupId)
                            ->whereHas('participantAssessments', function ($q) {
                                $q->whereHas('assessments', function ($q) {
                                    $q->whereDate('end_date', '>=', Carbon::now()->format('Y-m-d'));
                                });
                            })
                            ->get();

        dd($participants);
        // $pdf = Pdf::loadView('print.cards', compact('participants'))
        //     ->setPaper('A4', 'portrait');

        // return $pdf->stream();
    }
}
