<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Databases;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DatabaseConnectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        return response()->json(['message' => 'Not implemented.'], 501);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json(['message' => 'Not implemented.'], 501);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): \Illuminate\Http\JsonResponse
    {
        return response()->json(['message' => 'Not implemented.'], 501);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): \Illuminate\Http\JsonResponse
    {
        return response()->json(['message' => 'Not implemented.'], 501);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): \Illuminate\Http\JsonResponse
    {
        return response()->json(['message' => 'Not implemented.'], 501);
    }
}
