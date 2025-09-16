@extends('layouts.app')
@section('title','Thông tin người dùng')
@section('content')
<h2 class="fw-bold text-white mb-3">👤 Thông tin người dùng</h2>

<div class="card p-3">
    <p><strong>ID:</strong> {{ $user->id }}</p>
    <p><strong>Tên:</strong> {{ $user->name }}</p>
    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Vai trò:</strong> <span class="text-uppercase">{{ $user->role }}</span></p>
</div>

<a href="{{ route('admin.users.index') }}" class="btn btn-secondary mt-3">← Quay lại</a>
@endsection
