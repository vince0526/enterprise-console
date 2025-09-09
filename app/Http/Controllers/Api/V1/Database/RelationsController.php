<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Database;

use App\Http\Controllers\Controller;
use App\Models\CompanyUserRestriction;
use App\Models\DatabaseConnection;
use App\Services\Database\TableExplorer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RelationsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, DatabaseConnection $database_connection, TableExplorer $explorer): JsonResponse
    {
        $user = $request->user();
        /** @var \App\Models\User|null $user */
        if (! ($user?->hasRole('admin'))) {
            $allowed = CompanyUserRestriction::where('user_id', $user?->getKey())->where('database_connection_id', $database_connection->getKey())->exists();
            abort_unless((bool) $allowed, 403);
        }

        $table = $request->query('table');
        $type = $request->query('type', 'columns');

        if ($type === 'fks') {
            $data = $explorer->listForeignKeys($database_connection, (string) ($table ?? ''));
        } else {
            $data = $explorer->listColumns($database_connection, (string) ($table ?? ''));
        }

        return response()->json(['data' => $data]);
    }
}
