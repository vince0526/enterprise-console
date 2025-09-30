<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Emc;

use App\Http\Controllers\Controller;
use App\Http\Requests\CoreDatabaseRequest;
use App\Models\CoreDatabase;
use App\Models\DatabaseConnection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class CoreDatabaseController extends Controller
{
    public function index(Request $request): View
    {
        $activeTab = $request->query('tab', 'registry');

        $coreDbs = CoreDatabase::query()
            ->with(['owners', 'lifecycleEvents' => fn ($q) => $q->latest('effective_date'), 'links.databaseConnection'])
            ->orderBy('name')
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
        $data = $request->validated();

        CoreDatabase::create($data);

        return redirect()->route('emc.core.index')->with('status', 'Core database saved.');
    }

    public function destroy(CoreDatabase $core): RedirectResponse
    {
        $core->delete();

        return redirect()->route('emc.core.index')->with('status', 'Core database deleted.');
    }

    public function update(CoreDatabaseRequest $request, CoreDatabase $core): RedirectResponse
    {
        $core->update($request->validated());

        return redirect()->route('emc.core.index')->with('status', 'Core database updated.');
    }
}
