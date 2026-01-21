<?php

namespace App\Http\Controllers\Api;

use App\Action\GenerateRandomString;
use App\Action\LockUser;
use App\Action\UnlockUser;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function get($id)
    {
        try {
            $user = User::findOrFail($id);

            return response()->json([
                'message' => "SUKSES LOCK USER",
                'data' => $user
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ],
            400);
        }
    }

    public function lock(Request $request)
    {
        try {
            $id = auth()->user()->id;
            LockUser::execute($id);

            return response()->json(['message' => "SUKSES LOCK USER"]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ],
            400);
        }
    }

    public function unlock(Request $request)
    {
        $request->validate([
            'unlock_token' => 'required',
        ]);

        try {
            $id = auth()->user()->id;
            $unlockToken = $request->unlock_token;

            UnlockUser::execute($id, $unlockToken);

            return response()->json(['message' => "SUKSES UNLOCK USER"]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ],
            400);
        }
    }
}
