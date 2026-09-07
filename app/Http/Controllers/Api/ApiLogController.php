```php
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
            ApiLog::create([
                'url' => $request->fullUrl(),
                'json' => $request->getContent(),
                'user_id' => Auth::id() ?? null,
            ]);

            return response()->json([
                'message' => 'SUCCESS',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
```
