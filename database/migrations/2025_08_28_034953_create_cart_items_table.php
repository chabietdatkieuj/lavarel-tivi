<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Nếu bảng đã tồn tại -> chỉ bổ sung phần thiếu (nếu có) rồi thoát
        if (Schema::hasTable('cart_items')) {
            // Bổ sung cột price nếu thiếu
            if (!Schema::hasColumn('cart_items', 'price')) {
                Schema::table('cart_items', function (Blueprint $table) {
                    $table->decimal('price', 10, 2)->after('quantity');
                });
            }

            // (Tuỳ chọn) đảm bảo unique trên (cart_id, product_id)
            // Laravel chưa có hasIndex(), đặt tên index để tránh tạo trùng.
            try {
                Schema::table('cart_items', function (Blueprint $table) {
                    $table->unique(['cart_id', 'product_id'], 'cart_items_cart_id_product_id_unique');
                });
            } catch (\Throwable $e) {
                // bỏ qua nếu đã tồn tại
            }

            return; // ✅ stop: không create lại bảng
        }

        // Tạo mới khi CHƯA có bảng
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cart_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2);
            $table->timestamps();

            $table->foreign('cart_id')->references('id')->on('carts')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            $table->unique(['cart_id','product_id'], 'cart_items_cart_id_product_id_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
