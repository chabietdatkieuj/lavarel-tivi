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
    Schema::table('cart_items', function (Blueprint $table) {
        if (!Schema::hasColumn('cart_items', 'price')) {
            $table->decimal('price', 10, 2)->default(0)->after('quantity');
        }
        $table->unique(['cart_id', 'product_id'], 'cart_item_unique');
    });
}

public function down(): void
{
    Schema::table('cart_items', function (Blueprint $table) {
        if (Schema::hasColumn('cart_items', 'price')) {
            $table->dropColumn('price');
        }
        $table->dropUnique('cart_item_unique');
    });
}

};
