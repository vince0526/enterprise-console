<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Database;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TablesController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): \Illuminate\Http\JsonResponse
    {
        // Placeholder response
        return response()->json([]);
    }
}
