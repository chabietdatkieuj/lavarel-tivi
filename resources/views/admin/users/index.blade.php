@extends('layouts.app')
@section('title','Quản lý người dùng')

@section('content')
<h2 class="fw-bold text-white mb-3">👥 Quản lý người dùng</h2>



<div class="mb-2">
    <a href="{{ route('admin.users.create') }}" class="btn btn-gold">+ Thêm người dùng</a>
</div>

<div class="table-responsive">
<table class="table table-hover align-middle text-center">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Tên</th>
            <th>Email</th>
            <th>Vai trò</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
    @forelse($users as $u)
        <tr>
            <td>{{ $u->id }}</td>
            <td class="text-start">{{ $u->name }}</td>
            <td class="text-start">{{ $u->email }}</td>
            <td class="text-uppercase">{{ $u->role }}</td>
            <td>
                <a class="btn btn-info btn-sm" href="{{ route('admin.users.show',$u) }}">Xem</a>
                <a class="btn btn-primary btn-sm" href="{{ route('admin.users.edit',$u) }}">Sửa</a>
                <form class="d-inline" action="{{ route('admin.users.destroy',$u) }}" method="POST">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-sm" onclick="return confirm('Xoá người dùng này?')">Xoá</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="5" class="text-muted">Chưa có người dùng.</td></tr>
    @endforelse
    </tbody>
</table>
</div>

{{ $users->links() }}
@endsection
