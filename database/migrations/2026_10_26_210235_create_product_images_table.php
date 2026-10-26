<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->string('product_sku', 50);
            $table->string('path');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('product_sku')->references('sku')->on('products')->cascadeOnDelete();
            $table->index(['product_sku', 'sort_order']);
        });

        DB::table('products')
            ->whereNotNull('image')
            ->orderBy('sku')
            ->each(function (object $product): void {
                DB::table('product_images')->insert([
                    'product_sku' => $product->sku,
                    'path' => $product->image,
                    'sort_order' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
