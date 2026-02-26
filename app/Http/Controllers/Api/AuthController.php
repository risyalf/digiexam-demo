<?php

namespace App\Http\Controllers\Api;

use App\Enum\ParticipantStatus;
use App\Http\Controllers\Controller;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'nis' => 'required|string',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt(['nis' => $request->nis, 'password' => $request->password])) {
            return response()->json([
                'message' => 'NOMOR INDUK ATAU PASSWORD ANDA SALAH!'
            ], 401);
        }

        $user = auth()->user();        
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'LOGIN SUCCESSFUL',
            'data' => $user,
            'token' => $token
        ]);
    } 

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'LOGGED OUT'
        ]);
    }
}
