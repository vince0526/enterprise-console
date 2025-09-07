@extends('layouts.app')
@section('content')
<div class="container">
  <h1>Company Users</h1>
  <a class="btn btn-primary mb-3" href="{{ route('company-users.create') }}">Add</a>
  <table class="table table-striped">
    <thead><tr><th>ID</th><th>User</th><th>Company</th><th></th></tr></thead>
    <tbody>
      @foreach($rows as $r)
      <tr>
        <td>{{ $r->id }}</td>
        <td>{{ $r->user_id }}</td>
        <td>{{ $r->company_name }}</td>
        <td><a href="{{ route('company-users.edit',$r) }}">Edit</a></td>
      </tr>
      @endforeach
    </tbody>
  </table>
  {{ $rows->links() }}
</div>
@endsection
