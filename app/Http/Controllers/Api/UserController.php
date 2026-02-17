<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function get()
    {
        try {
            $user = auth()->user();

            return response()->json([
                'message' => "SUKSES GET DATA SISWA",
                'data' => $user
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ],
            400);
        }
    }
}
