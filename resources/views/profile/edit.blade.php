@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-3">Profile</h1>
    <form method="POST" action="/profile">
        @csrf
        @method('PATCH')
        <input type="text" name="name" value="{{ old('name', $user->name) }}">
        <input type="email" name="email" value="{{ old('email', $user->email) }}">
        <button type="submit">Save</button>
    </form>

    <form method="POST" action="/profile" class="mt-4">
        @csrf
        @method('DELETE')
        <input type="password" name="password" placeholder="Confirm password">
        <button type="submit">Delete account</button>
    </form>
</div>
@endsection
