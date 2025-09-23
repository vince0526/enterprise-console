@extends('emc.layout')

@section('title', 'Database Management - EMC')

@section('content')
@php
    $submenu = request()->get('submenu', 'connections');
@endphp

@include('emc.partials.db-toolbar')

@switch($submenu)
    @case('backup')
        @include('emc.db.backup')
        @break
    @case('performance')
        @include('emc.db.performance')
        @break
    @case('query')
        @include('emc.db.query')
        @break
    @case('replication')
        @include('emc.db.replication')
        @break
    @default
        @include('emc.db.connections')
@endswitch

@endsection