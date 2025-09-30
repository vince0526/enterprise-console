<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Emc;

use App\Http\Controllers\Controller;
use App\Http\Requests\CoreDatabaseLifecycleEventRequest;
use App\Models\CoreDatabaseLifecycleEvent;
use Illuminate\Http\RedirectResponse;

class CoreDatabaseLifecycleEventController extends Controller
{
    public function store(CoreDatabaseLifecycleEventRequest $request): RedirectResponse
    {
        CoreDatabaseLifecycleEvent::create($request->validated());

        return redirect()->route('emc.core.index', ['tab' => 'lifecycle'])->with('status', 'Lifecycle event added.');
    }

    public function destroy(CoreDatabaseLifecycleEvent $event): RedirectResponse
    {
        $event->delete();

        return redirect()->route('emc.core.index', ['tab' => 'lifecycle'])->with('status', 'Lifecycle event removed.');
    }
}
