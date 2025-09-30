<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Emc;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class EmcController extends Controller
{
    public function index(): RedirectResponse
    {
        $route = \Illuminate\Support\Facades\Route::has('emc.core.index')
            ? 'emc.core.index'
            : (\Illuminate\Support\Facades\Route::has('emc.db') ? 'emc.db' : 'dashboard');

        return redirect()->route($route);
    }

    public function db(): View
    {
        return view('emc.db');
    }

    public function tables(): View
    {
        return view('emc.tables');
    }

    public function files(): View
    {
        return view('emc.files');
    }

    public function users(): View
    {
        return view('emc.users');
    }

    public function reports(): View
    {
        return view('emc.reports');
    }

    public function ai(): View
    {
        return view('emc.ai');
    }

    public function comms(): View
    {
        return view('emc.comms');
    }

    public function settings(): View
    {
        return view('emc.settings');
    }

    public function activity(): View
    {
        return view('emc.activity');
    }

    public function about(): View
    {
        return view('emc.about');
    }

    public function filters(string $table): View
    {
        return view('emc.filters', ['table' => $table]);
    }
}
