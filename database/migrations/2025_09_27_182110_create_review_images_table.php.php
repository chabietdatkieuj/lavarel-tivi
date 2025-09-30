<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('review_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')
                  ->constrained('reviews')
                  ->cascadeOnDelete(); // xoá review sẽ xoá ảnh
            $table->string('path'); // đường dẫn lưu trên disk "public"
            $table->timestamps();

            $table->index('review_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_images');
    }
};
