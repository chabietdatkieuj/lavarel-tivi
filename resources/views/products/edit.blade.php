@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Sửa sản phẩm</h2>
    <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>Tên sản phẩm</label>
            <input type="text" name="name" class="form-control" value="{{ $product->name }}">
        </div>
        <div class="mb-3">
            <label>Mô tả</label>
            <textarea name="description" class="form-control">{{ $product->description }}</textarea>
        </div>
        <div class="mb-3">
            <label>Số lượng</label>
            <input type="number" name="quantity" class="form-control" value="{{ $product->quantity }}">
        </div>
        <div class="mb-3">
            <label>Giá</label>
            <input type="number" step="0.01" name="price" class="form-control" value="{{ $product->price }}">
        </div>
        <div class="mb-3">
            <label>Đặc điểm</label>
            <input type="text" name="features" class="form-control" value="{{ $product->features }}">
        </div>
        <div class="mb-3">
            <label>Danh mục</label>
            <select name="category_id" class="form-control">
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" @if($c->id == $product->category_id) selected @endif>
                        {{ $c->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Hình ảnh</label><br>
            @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" width="100" class="mb-2">
            @endif
            <input type="file" name="image" class="form-control">
        </div>
        <button type="submit" class="btn btn-success">Cập nhật</button>
    </form>
</div>
@endsection
