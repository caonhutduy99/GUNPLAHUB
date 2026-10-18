<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            // Mỗi giỏ hàng thuộc về đúng một khách hàng
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('product_sku', 50);
            $table->foreign('product_sku')->references('sku')->on('products')->restrictOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();

            // Một sản phẩm chỉ xuất hiện một dòng trong giỏ của một khách
            $table->unique(['user_id', 'product_sku']);
        });

        DB::statement('ALTER TABLE cart_items ADD CONSTRAINT chk_cart_items_qty CHECK (quantity > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
