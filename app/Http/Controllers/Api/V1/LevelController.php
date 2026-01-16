<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Level;

class LevelController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => Level::all(),
        ]);
    }
}
