<?php

namespace App\Action;

use App\Models\Participant;

class GenerateParticipantTestNumberAndPassword
{
    public static function execute(array $participantIds)
    {
        return Participant::query()
                ->whereIn('id', $participantIds)
                ->whereNull('test_number')
                ->whereNull('test_password')
                ->get()
                ->each(function ($participant) {
                    $participant->update([
                        'test_number' => GenerateTestNumber::execute($participant->id),
                        'test_password' => GenerateRandomString::execute(8),
                    ]);            
                });
    }
}
