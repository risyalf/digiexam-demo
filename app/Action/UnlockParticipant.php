<?php

namespace App\Action;

use App\Enum\ParticipantStatus;
use App\Models\ParticipantAssessment;
use Exception;

class UnlockParticipant
{
    public static function execute($id, $token)
    {
        $participant = ParticipantAssessment::findOrFail($id);

        if (!$participant->is_locked) {
            throw new Exception("SISWA TIDAK DALAM KONDISI TERKUNCI.");
        }

        if ($participant->unlock_token != $token) {
            throw new Exception("TOKEN SALAH!");
        }

        if ($participant->status == ParticipantStatus::FINISH) {
            throw new Exception("SISWA SUDAH SELESAI MENGERJAKAN UJIAN!");
        }

        $lastStatus = $participant->status;
        $participant->status = $participant->last_status;
        $participant->last_status = $lastStatus;
        $participant->unlock_token = null;
        return $participant->save();
    }
}
