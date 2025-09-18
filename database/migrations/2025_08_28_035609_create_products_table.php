<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tránh tạo lại khi bảng đã có sẵn trong DB
        if (Schema::hasTable('products')) {
            return;
        }

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');                 // Tên sản phẩm
            $table->text('description')->nullable();// Mô tả
            $table->integer('quantity')->default(0);// Số lượng
            $table->decimal('price', 10, 2);        // Giá
            $table->string('features')->nullable(); // Đặc điểm
            $table->string('image')->nullable();    // Hình ảnh

            // Khóa ngoại tới categories (xóa category -> xóa sản phẩm)
            $table->foreignId('category_id')
                  ->constrained('categories')
                  ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
