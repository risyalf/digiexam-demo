<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class ApkController extends Controller
{
    public function version()
    {
        try {
            return response()->json([
                'version' => '0.1.1',
                'url' => [
                    'full' => route('download.apk.filename', 'assessment_full.apk'),
                    'new' => route('download.apk.filename', 'assessment_v_new.apk'),
                    'old' => route('download.apk.filename', 'assessment_v_old.apk'),
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 400);
        }
    }
}
