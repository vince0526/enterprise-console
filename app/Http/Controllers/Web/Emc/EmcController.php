<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Emc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmcController extends Controller
{
    public function index()
    {
        return redirect()->route('emc.db');
    }

    public function db()
    {
        return view('emc.db');
    }

    public function tables()
    {
        return view('emc.tables');
    }

    public function files()
    {
        return view('emc.files');
    }

    public function users()
    {
        return view('emc.users');
    }

    public function reports()
    {
        return view('emc.reports');
    }

    public function ai()
    {
        return view('emc.ai');
    }

    public function comms()
    {
        return view('emc.comms');
    }

    public function settings()
    {
        return view('emc.settings');
    }

    public function activity()
    {
        return view('emc.activity');
    }

    public function about()
    {
        return view('emc.about');
    }

    public function filters(string $table)
    {
        return view('emc.filters', ['table' => $table]);
    }
}
