<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Personnel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PersonnelController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $personnel = Personnel::query()
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return response()->json($personnel);
    }

    public function show(Personnel $personnel): JsonResponse
    {
        return response()->json($personnel);
    }
}
