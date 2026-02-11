<?php

namespace App\Action;

use App\Models\AssessmentToken;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CreateToken
{
    public static function execute(array $data): AssessmentToken
    {
        return AssessmentToken::create([
            'value' => GenerateRandomString::execute(),
            'expired_time' => $data['expired_time'],
            'expired_until' => Carbon::now()->addMinutes((int)$data['expired_time']),
            'assessment_id' => $data['all_module'] ? null : $data['assessment_id'],
            'all_module' => $data['all_module'] == 1 ? DB::raw('true') : DB::raw('false'),
        ]);
    }
}
