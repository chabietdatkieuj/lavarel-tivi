<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');              // khách
            $table->unsignedBigInteger('admin_id')->nullable(); // admin đang phụ trách
            $table->string('status', 20)->default('open');      // open/closed
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('admin_id')->references('id')->on('users')->nullOnDelete();
            $table->index(['user_id', 'status']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
