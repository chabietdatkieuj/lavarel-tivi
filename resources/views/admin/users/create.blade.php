@extends('layouts.app')
@section('title','Thêm người dùng')
@section('content')
<h2 class="fw-bold text-white mb-3">➕ Thêm người dùng</h2>

<form action="{{ route('admin.users.store') }}" method="POST" class="row g-3">
@csrf
<div class="col-md-6">
    <label class="form-label">Tên</label>
    <input type="text" name="name" class="form-control bg-dark text-white" required>
</div>
<div class="col-md-6">
    <label class="form-label">Email</label>
    <input type="email" name="email" class="form-control bg-dark text-white" required>
</div>
<div class="col-md-6">
    <label class="form-label">Mật khẩu</label>
    <input type="password" name="password" class="form-control bg-dark text-white" required>
</div>
<div class="col-md-6">
    <label class="form-label">Vai trò</label>
    <select name="role" class="form-select bg-dark text-white" required>
        <option value="customer">Customer</option>
        <option value="admin">Admin</option>
        <option value="user">User</option>
    </select>
</div>
<div class="col-12">
    <button class="btn btn-gold">Lưu</button>
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Huỷ</a>
</div>
</form>
@endsection
