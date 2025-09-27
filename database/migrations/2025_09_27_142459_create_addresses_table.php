<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('addresses', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->string('receiver_name');     // tên người nhận
        $table->string('receiver_phone');    // SĐT
        $table->string('full_address');      // địa chỉ đầy đủ
        $table->boolean('is_default')->default(false);
        $table->timestamps();
    });
}
public function down(): void
{
    Schema::dropIfExists('addresses');
}
};
