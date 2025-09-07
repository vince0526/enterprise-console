@extends('layouts.app')
@section('content')
<div class="container">
  <h1>User Restrictions</h1>
  <a class="btn btn-primary mb-3" href="{{ route('user-restrictions.create') }}">Add</a>
  <table class="table table-striped">
    <thead><tr><th>ID</th><th>User</th><th>DB Conn</th><th>Allowed Tables</th><th></th></tr></thead>
    <tbody>
      @foreach($rows as $r)
      <tr>
        <td>{{ $r->id }}</td>
        <td>{{ optional($r->user)->name ?? $r->user_id }}</td>
        <td>{{ optional($r->databaseConnection)->name ?? $r->database_connection_id }}</td>
        <td>{{ is_array($r->allowed_tables) ? implode(', ', $r->allowed_tables) : '' }}</td>
        <td><a href="{{ route('user-restrictions.edit',$r) }}">Edit</a></td>
      </tr>
      @endforeach
    </tbody>
  </table>
  {{ $rows->links() }}
</div>
@endsection
