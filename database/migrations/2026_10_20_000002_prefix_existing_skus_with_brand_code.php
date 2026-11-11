<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * SKU mới có dạng {MÃ HÃNG}-...: các sản phẩm hiện có đều thuộc Bandai
     * nên thêm tiền tố "BAN-" (HG-UC-001 -> BAN-HG-UC-001).
     * Cập nhật luôn giỏ hàng và chi tiết đơn hàng đang tham chiếu SKU cũ.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        // MySQL dùng CONCAT(), SQLite dùng toán tử || để nối chuỗi
        $concat = fn (string $column) => DB::getDriverName() === 'sqlite' ? "'BAN-' || {$column}" : "CONCAT('BAN-', {$column})";

        DB::statement("UPDATE products SET sku = {$concat('sku')} WHERE sku NOT LIKE 'BAN-%'");
        DB::statement("UPDATE cart_items SET product_sku = {$concat('product_sku')} WHERE product_sku NOT LIKE 'BAN-%'");
        DB::statement("UPDATE order_details SET product_sku = {$concat('product_sku')} WHERE product_sku NOT LIKE 'BAN-%'");

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        DB::statement("UPDATE products SET sku = SUBSTR(sku, 5) WHERE sku LIKE 'BAN-%'");
        DB::statement("UPDATE cart_items SET product_sku = SUBSTR(product_sku, 5) WHERE product_sku LIKE 'BAN-%'");
        DB::statement("UPDATE order_details SET product_sku = SUBSTR(product_sku, 5) WHERE product_sku LIKE 'BAN-%'");

        Schema::enableForeignKeyConstraints();
    }
};
