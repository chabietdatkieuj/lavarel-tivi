@extends('layouts.app')
@section('title','Sửa người dùng')
@section('content')
<h2 class="fw-bold text-white mb-3">✏️ Sửa người dùng</h2>

<form action="{{ route('admin.users.update',$user) }}" method="POST" class="row g-3">
@csrf @method('PUT')
<div class="col-md-6">
    <label class="form-label">Tên</label>
    <input type="text" name="name" value="{{ $user->name }}" class="form-control bg-dark text-white" required>
</div>
<div class="col-md-6">
    <label class="form-label">Email</label>
    <input type="email" name="email" value="{{ $user->email }}" class="form-control bg-dark text-white" required>
</div>
<div class="col-md-6">
    <label class="form-label">Mật khẩu (để trống nếu không đổi)</label>
    <input type="password" name="password" class="form-control bg-dark text-white">
</div>
<div class="col-md-6">
    <label class="form-label">Vai trò</label>
    <select name="role" class="form-select bg-dark text-white" required>
        <option value="customer" @selected($user->role==='customer')>Customer</option>
        <option value="user" @selected($user->role==='user')>User</option>
        <option value="admin" @selected($user->role==='admin')>Admin</option>
    </select>
</div>
<div class="col-12">
    <button class="btn btn-gold">Cập nhật</button>
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Quay lại</a>
</div>
</form>
@endsection
