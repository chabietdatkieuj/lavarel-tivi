<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');

            // Thông tin giao nhận
            $table->string('shipping_name');
            $table->string('shipping_phone', 30);
            $table->string('shipping_address');

            // Tổng tiền tại thời điểm đặt
            $table->decimal('total_amount', 12, 2)->default(0);

            // Phương thức thanh toán
            $table->string('payment_method')->default('cod'); // cod|momo|vnpay
            $table->string('status')->default('pending');     // pending|paid|cancelled|failed

            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
