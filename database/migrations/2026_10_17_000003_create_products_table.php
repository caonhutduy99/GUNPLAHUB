<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->string('sku', 50)->primary();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->string('name', 255);
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->string('grade', 20);
            $table->string('scale', 20);
            $table->string('series', 50);
            $table->string('image')->nullable();
            $table->enum('status', ['Còn hàng', 'Hết hàng', 'Ngừng kinh doanh'])->default('Còn hàng');
            $table->timestamps();
        });

        // Ràng buộc: giá phải là số dương
        DB::statement('ALTER TABLE products ADD CONSTRAINT chk_products_price CHECK (price > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
