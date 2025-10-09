<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Emc;

use App\Http\Controllers\Controller;
use App\Models\SavedView;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * SavedViewController
 *
 * Lightweight JSON API for persisting and managing user saved views (filter presets)
 * for the Core Databases registry. Endpoints are intentionally minimal to reduce
 * backend complexity—authorization is user ownership based.
 */
class SavedViewController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorizeAccess();
        $context = $request->query('context', 'core_databases');
        $views = SavedView::query()
            ->where('user_id', Auth::id())
            ->where('context', $context)
            ->orderBy('name')
            ->get(['id', 'name', 'filters']);

        return response()->json($views);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorizeAccess();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'context' => ['sometimes', 'string', 'max:64'],
            'filters' => ['required', 'array'],
        ]);
        $context = $data['context'] ?? 'core_databases';
        $view = SavedView::updateOrCreate([
            'user_id' => Auth::id(),
            'context' => $context,
            'name' => $data['name'],
        ], [
            'filters' => $data['filters'],
        ]);

        return response()->json($view, 201);
    }

    public function destroy(SavedView $savedView): JsonResponse
    {
        $this->authorizeAccess();
        abort_unless($savedView->user_id === Auth::id(), 403);
        $savedView->delete();

        return response()->json(['status' => 'deleted']);
    }

    private function authorizeAccess(): void
    {
        if (! (bool) config('app.dev_auto_login', false)) {
            // Generic gate: require authenticated user
            abort_unless(Auth::check(), 401);
        }
    }
}
