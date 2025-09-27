<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users','phone')) {
                $table->string('phone', 30)->nullable()->after('email');
            }
            if (!Schema::hasColumn('users','address')) {
                $table->string('address', 255)->nullable()->after('phone');
            }
            // Tuỳ chọn: nếu muốn phân quyền
            // if (!Schema::hasColumn('users','role')) {
            //     $table->string('role', 20)->default('customer')->after('address');
            // }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users','address')) $table->dropColumn('address');
            if (Schema::hasColumn('users','phone'))   $table->dropColumn('phone');
            // if (Schema::hasColumn('users','role'))    $table->dropColumn('role');
        });
    }
};
