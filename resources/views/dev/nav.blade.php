@extends('layouts.guest')

@section('content')
    <div class="mx-auto max-w-3xl py-8 space-y-6">
        <h1 class="text-2xl font-bold">Developer Navigation</h1>
        <p class="text-sm text-gray-400">Quick links to application screens (auth bypass = {{ env('DEV_AUTO_LOGIN') ? 'on' : 'off' }}). Remove this file & route before committing to production.</p>

        <div class="grid md:grid-cols-2 gap-4">
            <div class="p-4 border border-gray-700 rounded">
                <h2 class="font-semibold mb-2">Core</h2>
                <ul class="list-disc list-inside space-y-1 text-sm">
                    <li><a class="text-indigo-400 hover:underline" href="/">Welcome</a></li>
                    <li><a class="text-indigo-400 hover:underline" href="/dashboard">Dashboard</a></li>
                    <li><a class="text-indigo-400 hover:underline" href="/profile">Profile Edit</a></li>
                </ul>
            </div>
            <div class="p-4 border border-gray-700 rounded">
                <h2 class="font-semibold mb-2">Database Resources</h2>
                <ul class="list-disc list-inside space-y-1 text-sm">
                    <li><a class="text-indigo-400 hover:underline" href="/database/company-users">Company Users Index</a></li>
                    <li><a class="text-indigo-400 hover:underline" href="/database/user-restrictions">User Restrictions Index</a></li>
                </ul>
            </div>
            <div class="p-4 border border-gray-700 rounded md:col-span-2">
                <h2 class="font-semibold mb-2">Auth & Recovery</h2>
                <ul class="list-disc list-inside space-y-1 text-sm">
                    <li><a class="text-indigo-400 hover:underline" href="/login">Login (should be bypassed)</a></li>
                    <li><a class="text-indigo-400 hover:underline" href="/register">Register</a></li>
                    <li><a class="text-indigo-400 hover:underline" href="/forgot-password">Forgot Password</a></li>
                    <li><a class="text-indigo-400 hover:underline" href="/auth/recover-username">Recover Username (POST)</a></li>
                </ul>
            </div>
        </div>

        <div class="mt-8 p-4 bg-yellow-900/40 border border-yellow-700 rounded text-sm">
            <p><strong>Reminder:</strong> Remove <code>resources/views/dev/nav.blade.php</code> and the <code>/dev</code> route before merging to any shared branch.</p>
        </div>
    </div>
@endsection
