<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('product_sku', 50);
            $table->foreign('product_sku')->references('sku')->on('products')->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 10, 2);

            // Khóa chính kép theo thiết kế
            $table->primary(['order_id', 'product_sku']);
        });

        DB::statement('ALTER TABLE order_details ADD CONSTRAINT chk_order_details_qty CHECK (quantity > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
