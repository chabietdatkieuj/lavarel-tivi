<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders','voucher_code')) {
                $table->string('voucher_code')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('orders','discount_amount')) {
                $table->decimal('discount_amount',12,2)->default(0)->after('voucher_code');
            }
        });
    }
    public function down(): void {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders','voucher_code')) $table->dropColumn('voucher_code');
            if (Schema::hasColumn('orders','discount_amount')) $table->dropColumn('discount_amount');
        });
    }
};
