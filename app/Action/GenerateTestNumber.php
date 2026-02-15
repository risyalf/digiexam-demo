<?php

namespace App\Action;

use App\Models\Module;
use App\Models\Participant;
use Carbon\Carbon;

class GenerateTestNumber
{
    public static function execute(string $participantId)
    {
        // (Nomer urut peserta ) / (modul) / (group) / (tahun)
        $participant = Participant::find($participantId);
        $orderNumber = $participant->order_number;
        $modul = $participant->module->name;
        $group = $participant->participantGroup->name;
        $year = Carbon::now()->format('Y');
        $noTest = "$orderNumber/$modul/$group/$year";
        return strtoupper($noTest);
    }
}
