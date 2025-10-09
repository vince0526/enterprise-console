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
        $this->authorize('viewAny', SavedView::class);
        $context = $request->query('context', 'core_databases');
        $q = trim((string) $request->query('q', ''));
        $limitParam = $request->query('limit', '50');
        $limit = (int) (is_array($limitParam) ? 50 : $limitParam);
        $limit = $limit > 0 ? min($limit, 100) : 50; // cap

        $views = SavedView::query()
            ->where('user_id', Auth::id())
            ->where('context', $context)
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where('name', 'like', "%{$q}%");
            })
            ->orderBy('name')
            ->limit($limit)
            ->get(['id', 'name', 'filters']);

        // Collect metadata
        $total = SavedView::query()
            ->where('user_id', Auth::id())
            ->where('context', $context)
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where('name', 'like', "%{$q}%");
            })
            ->count();

        return response()
            ->json($views)
            ->header('X-SavedViews-Total', (string) $total)
            ->header('X-SavedViews-Limit', (string) $limit)
            ->header('X-SavedViews-Returned', (string) $views->count());
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', SavedView::class);
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
        $this->authorize('delete', $savedView);
        $savedView->delete();

        return response()->json(['status' => 'deleted']);
    }

    // Legacy helper retained (unused) for potential compatibility
    private function authorizeAccess(): void {}
}
