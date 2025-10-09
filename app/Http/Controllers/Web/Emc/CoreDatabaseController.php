<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Emc;

use App\Http\Controllers\Controller;
use App\Http\Requests\CoreDatabaseRequest;
use App\Models\CoreDatabase;
use App\Models\DatabaseConnection;
use App\Services\CoreDatabaseDdlGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

/**
 * CoreDatabaseController
 *
 * Purpose: Web controller for the EMC Core Databases Workbench UI.
 *
 * Key responsibilities:
 * - Render the registry with filters/sorting (index)
 * - Persist records via CRUD (store/update/destroy)
 * - CSV export of the registry (exportCsv)
 * - Generate DDL based on engine + functional scopes (generateDdl)
 *
 * Where to customize safely:
 * - Filters/sorting: see the index() query ->when(...) and the allowed sort fields
 * - CSV columns/order: adjust $headers and the mapped values in exportCsv()
 * - DDL behavior: prefer changing App\Services\CoreDatabaseDdlGenerator to keep controller slim
 * - Authorization: see policy (App\Policies\CoreDatabasePolicy) and calls to $this->authorize(...)
 */
class CoreDatabaseController extends Controller
{
    public function index(Request $request): View
    {
        // Authorization for listing/registry view. Skip in dev with auto-login enabled.
        if (! (bool) config('app.dev_auto_login', false)) {
            $this->authorize('viewAny', CoreDatabase::class);
        }

        // UI state and filter inputs from query string.
        $activeTab = $request->query('tab', 'registry');
        $q = trim((string) $request->query('q', ''));
        $tier = $request->query('tier');
        $vcStage = $request->query('vc_stage');
        $engine = $request->query('engine');
        $env = $request->query('env');
        $scopes = (array) $request->query('scopes', []);
        // Only allow sorting by a whitelisted set of columns to prevent SQL injection.
        // Backward-compat: accept legacy 'sort'/'direction' alongside 'sortBy'/'sortDir'.
        // UI aliases: map 'environment' -> 'env' and 'platform' -> 'engine'.
        $allowedSorts = ['name', 'engine', 'env', 'tier', 'owner', 'status', 'updated_at'];
        $incomingSort = $request->query('sortBy', $request->query('sort', 'name'));
        $aliasMap = [
            'environment' => 'env',
            'platform' => 'engine',
        ];
        $sortQuery = $aliasMap[$incomingSort] ?? $incomingSort;
        $dirQuery = $request->query('sortDir', $request->query('direction', 'asc'));
        $sortBy = in_array($sortQuery, $allowedSorts, true) ? $sortQuery : 'name';
        $sortDir = $dirQuery === 'desc' ? 'desc' : 'asc';

        // The registry query: extend or add filters via ->when(...) blocks.
        $coreDbs = CoreDatabase::query()
            ->with(['owners', 'lifecycleEvents' => fn ($q) => $q->latest('effective_date'), 'links.databaseConnection'])
            ->when($tier, fn ($qb) => $qb->where('tier', $tier))
            ->when($engine, fn ($qb) => $qb->where('engine', $engine))
            ->when($env, fn ($qb) => $qb->where('env', $env))
            ->when($vcStage, fn ($qb) => $qb->where('vc_stage', $vcStage))
            ->when(! empty($scopes), function ($qb) use ($scopes) {
                foreach ($scopes as $s) {
                    $qb->whereJsonContains('functional_scopes', $s);
                }
            })
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                        ->orWhere('tax_path', 'like', "%{$q}%")
                        ->orWhere('owner', 'like', "%{$q}%")
                        ->orWhere('engine', 'like', "%{$q}%")
                        ->orWhere('env', 'like', "%{$q}%")
                        ->orWhere('tier', 'like', "%{$q}%");
                });
            })
            ->orderBy($sortBy, $sortDir)
            // Provide deterministic secondary ordering to stabilize tests (especially for engine/env sorts)
            ->when(in_array($sortBy, ['engine', 'env'], true), fn ($qb) => $qb->orderBy('name'))
            ->get();

        // Optional: map linked_connection (by name) to existing DatabaseConnection IDs for linking.
        // If you introduce a different linking strategy, modify this map or remove it entirely.
        $names = $coreDbs->pluck('linked_connection')->filter()->unique()->values();
        $connectionByName = collect();
        if ($names->isNotEmpty() && Schema::hasTable('database_connections')) {
            $connectionByName = DatabaseConnection::query()
                ->whereIn('name', $names)
                ->get(['id', 'name'])
                ->keyBy('name');
        }

        return view('emc.core', compact('coreDbs', 'connectionByName', 'activeTab'));
    }

    public function store(CoreDatabaseRequest $request): RedirectResponse
    {
        // Creation requires policy permission; validation handled by CoreDatabaseRequest.
        $this->authorize('create', CoreDatabase::class);
        $data = $request->validated();

        CoreDatabase::create($data);

        return redirect()->route('emc.core.index')->with('status', 'Core database saved.');
    }

    public function destroy(CoreDatabase $core): RedirectResponse
    {
        // Soft-delete vs hard-delete: switch to $core->update(['status' => 'archived']) if needed.
        $this->authorize('delete', $core);
        $core->delete();

        return redirect()->route('emc.core.index')->with('status', 'Core database deleted.');
    }

    public function update(CoreDatabaseRequest $request, CoreDatabase $core): RedirectResponse
    {
        // Update specific fields by whitelisting in the FormRequest rules/fillable model fields.
        $this->authorize('update', $core);
        $core->update($request->validated());

        return redirect()->route('emc.core.index')->with('status', 'Core database updated.');
    }

    public function exportCsv(Request $request): Response
    {
        // To customize CSV: edit $headers and the mapped row values below.
        $this->authorize('viewAny', CoreDatabase::class);
        $rows = CoreDatabase::query()->orderBy('name')->get();
        $headers = ['id', 'name', 'engine', 'env', 'tier', 'tax_path', 'owner', 'status', 'updated_at'];
        $csv = implode(',', $headers)."\n";
        foreach ($rows as $r) {
            $csv .= implode(',', array_map(fn ($v) => '"'.str_replace('"', '""', (string) ($v ?? '')).'"', [
                $r->id,
                $r->name,
                $r->engine,
                $r->env,
                $r->tier,
                $r->tax_path,
                $r->owner,
                $r->status,
                $r->updated_at,
            ]))."\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="core_databases_registry.csv"',
        ]);
    }

    public function generateDdl(Request $request, CoreDatabaseDdlGenerator $generator): Response
    {
        // Generate engine-specific DDL from functional scopes via dedicated service.
        $this->authorize('viewAny', CoreDatabase::class);
        $engine = (string) $request->input('engine', 'PostgreSQL');
        $functionalScopes = (array) $request->input('functional_scopes', []);
        $sql = $generator->generate($engine, $functionalScopes);

        return response($sql, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="generated.sql"',
        ]);
    }
}
