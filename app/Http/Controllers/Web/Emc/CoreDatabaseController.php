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

class CoreDatabaseController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', CoreDatabase::class);
        $activeTab = $request->query('tab', 'registry');
        $q = trim((string) $request->query('q', ''));
        $tier = $request->query('tier');
        $vcStage = $request->query('vc_stage');
        $engine = $request->query('engine');
        $env = $request->query('env');
        $scopes = (array) $request->query('scopes', []);
        $sortBy = in_array($request->query('sortBy'), ['name', 'engine', 'env', 'tier', 'owner', 'status', 'updated_at'], true) ? $request->query('sortBy') : 'name';
        $sortDir = $request->query('sortDir') === 'desc' ? 'desc' : 'asc';

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
            ->get();

        // Optional: map linked_connection (by name) to existing DatabaseConnection IDs for linking
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
        $this->authorize('create', CoreDatabase::class);
        $data = $request->validated();

        CoreDatabase::create($data);

        return redirect()->route('emc.core.index')->with('status', 'Core database saved.');
    }

    public function destroy(CoreDatabase $core): RedirectResponse
    {
        $this->authorize('delete', $core);
        $core->delete();

        return redirect()->route('emc.core.index')->with('status', 'Core database deleted.');
    }

    public function update(CoreDatabaseRequest $request, CoreDatabase $core): RedirectResponse
    {
        $this->authorize('update', $core);
        $core->update($request->validated());

        return redirect()->route('emc.core.index')->with('status', 'Core database updated.');
    }

    public function exportCsv(Request $request): Response
    {
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
