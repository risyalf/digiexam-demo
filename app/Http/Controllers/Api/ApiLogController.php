<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiLogController extends Controller
{
    public function store(Request $request)
    {
        try {
            $json = $request->json();
            $url = $request->url();
            ApiLog::create([
                "url" => $url,
                "json" => $json,
                "user_id" => Auth::id() ?? null
            ]);

            return response()->json(["message" => "SUCCESS"]);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    "message" => $th->getMessage(),
                ],
                400,
            );
        }
    }
}
