<?php

namespace App\Action;

use App\Models\Module;
use App\Models\Participant;
use App\Models\ParticipantGroup;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

use function Symfony\Component\Clock\now;

class PrintLoginCard
{
    public static function execute(string $moduleId, ?string $groupId)
    {
        $module = Module::find($moduleId);
        $moduleName = $module->name;
        $participants = Participant::query()
                            ->with(['user', 'participantGroup'])
                            ->where('module_id', $moduleId)
                            ->when($groupId, fn($q) => $q->where('participant_group_id', $groupId))
                            ->whereHas('participantAssessments.assessment', function ($q) {
                                $q->whereDate('end_date', '>=', Carbon::now()->format('Y-m-d'));
                            })
                            ->get()
                            ->map(function ($participant) {
                                $name = $participant->user->name ?? '';
                                $participant->user->name = htmlspecialchars(iconv('UTF-8', 'UTF-8//IGNORE', $name), ENT_QUOTES, 'UTF-8');
                                
                                return $participant;
                            });

        $pdf = Pdf::loadView('print.login-cards', compact('participants', 'moduleName'))
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'login-cards.pdf');
    }
}
