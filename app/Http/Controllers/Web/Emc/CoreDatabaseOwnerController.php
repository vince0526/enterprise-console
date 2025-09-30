<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Emc;

use App\Http\Controllers\Controller;
use App\Http\Requests\CoreDatabaseOwnerRequest;
use App\Models\CoreDatabaseOwner;
use Illuminate\Http\RedirectResponse;

class CoreDatabaseOwnerController extends Controller
{
    public function store(CoreDatabaseOwnerRequest $request): RedirectResponse
    {
        CoreDatabaseOwner::create($request->validated());

        return redirect()->route('emc.core.index', ['tab' => 'ownership'])->with('status', 'Owner added.');
    }

    public function destroy(CoreDatabaseOwner $owner): RedirectResponse
    {
        $owner->delete();

        return redirect()->route('emc.core.index', ['tab' => 'ownership'])->with('status', 'Owner removed.');
    }
}
