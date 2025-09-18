<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->cascadeOnDelete();
            $table->tinyInteger('rating'); // 1..5
            $table->text('comment')->nullable();
            $table->timestamps();

            // mỗi user chỉ review 1 lần cho 1 product trong 1 order
            $table->unique(['user_id','product_id','order_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('reviews');
    }
};
