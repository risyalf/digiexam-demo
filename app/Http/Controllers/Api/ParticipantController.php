<?php

namespace App\Http\Controllers\Api;

use App\Action\LockParticipant;
use App\Action\UnlockParticipant;
use App\Http\Controllers\Controller;
use App\Models\Participant;
use Exception;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'test_number' => 'required',
                'test_password' => 'required',
            ]);
            
            $testNumber = $request->test_number;
            $testPassword = $request->test_password;

            $participant = Participant::query()
                            ->join('users as u', 'u.id', 'participants.user_id')
                            ->where([
                                'test_number' => $testNumber,
                                'test_password' => $testPassword,
                            ])
                            ->first([
                                'participants.id',
                                'u.name',
                                'u.nis'
                            ]);

            if (!$participant) {
                throw new Exception("NOMOR TEST ATAU PASSWORD ANDA SALAH. SILAHKAN MASUKKAN DATA YANG BENAR ATAU HUBUNGI OPERATOR.");
            }

            $token = $participant->createToken('participant-token')->plainTextToken;

            return response()->json([
                'message' => "SUKSES GET DATA SISWA",
                'token' => $token,
                'data' => $participant
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ],
            400);
        }
    }

    public function logout()
    {
        try {
            auth()->logout();

            return response()->json([
                'message' => "SUKSES GET DATA SISWA",
                'token' => $token,
                'data' => $participant
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ],
            400);
        }
    }

    public function get($id)
    {
        try {
            $participant = Participant::findOrFail($id);

            return response()->json([
                'message' => "SUKSES GET DATA SISWA",
                'data' => $participant
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ],
            400);
        }
    }
}
