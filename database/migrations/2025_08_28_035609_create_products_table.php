<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');                // Tên sản phẩm
            $table->text('description')->nullable(); // Mô tả
            $table->integer('quantity')->default(0); // Số lượng
            $table->decimal('price', 10, 2);       // Giá
            $table->string('features')->nullable(); // Các đặc điểm
            $table->string('image')->nullable();    // Hình ảnh
            $table->unsignedBigInteger('category_id'); // Khóa phụ

            // Tạo quan hệ
            $table->foreign('category_id')
                  ->references('id')->on('categories')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
