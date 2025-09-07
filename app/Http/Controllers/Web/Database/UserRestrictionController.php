<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Database;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserRestrictionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Contracts\View\View
    {
        // Placeholder response
        return view('welcome');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Illuminate\Contracts\View\View
    {
        // Placeholder response
        return view('welcome');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        // Placeholder response
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): \Illuminate\Contracts\View\View
    {
        // Placeholder response
        return view('welcome');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): \Illuminate\Contracts\View\View
    {
        // Placeholder response
        return view('welcome');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): \Illuminate\Http\RedirectResponse
    {
        // Placeholder response
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): \Illuminate\Http\RedirectResponse
    {
        // Placeholder response
        return redirect()->back();
    }
}
