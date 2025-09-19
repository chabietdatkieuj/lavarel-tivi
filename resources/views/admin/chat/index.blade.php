@extends('layouts.app')
@section('title','Chat – Hộp thư')

@section('content')
<h2 class="mb-3">💬 Hỗ trợ trực tuyến</h2>
<table class="table">
  <thead><tr><th>ID</th><th>Khách</th><th>Admin</th><th>Trạng thái</th><th>Cập nhật</th><th></th></tr></thead>
  <tbody>
    @foreach($convs as $c)
      <tr>
        <td>#{{ $c->id }}</td>
        <td>{{ $c->user->name ?? 'User '.$c->user_id }}</td>
        <td>{{ $c->admin?->name ?? '—' }}</td>
        <td>{{ $c->status }}</td>
        <td>{{ $c->updated_at->format('d/m H:i') }}</td>
        <td><a class="btn btn-sm btn-primary" href="{{ route('admin.chats.show',$c) }}">Mở</a></td>
      </tr>
    @endforeach
  </tbody>
</table>
{{ $convs->links() }}
@endsection
