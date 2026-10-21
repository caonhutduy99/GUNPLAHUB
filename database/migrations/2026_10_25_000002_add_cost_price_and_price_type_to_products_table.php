<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('cost_price', 10, 2)->default(0)->after('price');
            $table->string('price_type', 50)->default('Giá bán lẻ')->after('cost_price');
        });

        // Thiết lập giá nhập mặc định cho các sản phẩm hiện có (ước tính 70% giá bán) để thỏa mãn quy tắc price >= cost_price
        DB::statement('UPDATE products SET cost_price = ROUND(price * 0.7) WHERE cost_price = 0 OR cost_price IS NULL');
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['cost_price', 'price_type']);
        });
    }
};
