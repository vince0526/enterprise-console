<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Restrictions;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Restrictions\CompanyUserRestrictionRequest;
use App\Http\Resources\Api\V1\Databases\DatabaseConnectionResource;
use App\Models\CompanyUserRestriction;
use Illuminate\Http\JsonResponse;

class CompanyUserRestrictionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        abort_unless((bool) auth()->user()?->can('viewAny', CompanyUserRestriction::class), 403);

        $list = CompanyUserRestriction::query()->with('databaseConnection')->get();

        return response()->json(['data' => $list->map(fn (CompanyUserRestriction $r) => [
            'id' => $r->getKey(),
            'user_id' => $r->user_id,
            'database_connection' => new DatabaseConnectionResource($r->databaseConnection),
        ])]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CompanyUserRestrictionRequest $request): JsonResponse
    {
        $data = $request->validated();

        $model = CompanyUserRestriction::create($data);

        return response()->json(['data' => $model], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $model = CompanyUserRestriction::with('databaseConnection')->findOrFail($id);

        abort_unless((bool) auth()->user()?->can('view', $model), 403);

        return response()->json(['data' => $model]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompanyUserRestrictionRequest $request, string $id): JsonResponse
    {
        $model = CompanyUserRestriction::findOrFail($id);

        abort_unless((bool) auth()->user()?->can('update', $model), 403);

        $model->fill($request->validated());
        $model->save();

        return response()->json(['data' => $model]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $model = CompanyUserRestriction::findOrFail($id);

        abort_unless((bool) auth()->user()?->can('delete', $model), 403);

        $model->delete();

        return response()->json(['deleted' => true]);
    }
}
