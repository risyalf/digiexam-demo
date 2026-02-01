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
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'INVALID CREDENTIAL'
            ], 401);
        }

        $user = auth()->user();
        $token = $user->createToken('api-token')->plainTextToken;

        Participant::firstOrCreate([
            'user_id' => $user->id,
            'status' => ParticipantStatus::LOGGED_IN,
        ]);

        return response()->json([
            'message' => 'LOGIN SUCCESSFUL',
            'user' => $user,
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
