<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Emc;

use App\Http\Controllers\Controller;
use App\Http\Requests\CoreDatabaseLinkRequest;
use App\Models\CoreDatabaseLink;
use Illuminate\Http\RedirectResponse;

class CoreDatabaseLinkController extends Controller
{
    public function store(CoreDatabaseLinkRequest $request): RedirectResponse
    {
        CoreDatabaseLink::create($request->validated());

        return redirect()->route('emc.core.index', ['tab' => 'links'])->with('status', 'Link added.');
    }

    public function destroy(CoreDatabaseLink $link): RedirectResponse
    {
        $link->delete();

        return redirect()->route('emc.core.index', ['tab' => 'links'])->with('status', 'Link removed.');
    }
}
