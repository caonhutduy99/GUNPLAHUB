<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_histories', function (Blueprint $table) {
            $table->id();
            $table->string('product_sku', 50);
            $table->foreign('product_sku')->references('sku')->on('products')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('old_cost_price', 10, 2)->nullable();
            $table->decimal('cost_price', 10, 2);
            $table->decimal('old_price', 10, 2)->nullable();
            $table->decimal('price', 10, 2);
            $table->string('price_type', 50)->default('Giá bán lẻ');
            $table->string('note', 255)->nullable();
            $table->timestamps();
        });

        // Tạo bản ghi lịch sử ban đầu cho các sản phẩm hiện có
        $products = DB::table('products')->get(['sku', 'price', 'cost_price', 'price_type', 'created_at']);
        $adminId = DB::table('users')->where('role', 'admin')->value('id');

        foreach ($products as $p) {
            DB::table('price_histories')->insert([
                'product_sku' => $p->sku,
                'user_id' => $adminId,
                'old_cost_price' => null,
                'cost_price' => $p->cost_price ?? 0,
                'old_price' => null,
                'price' => $p->price,
                'price_type' => $p->price_type ?? 'Giá bán lẻ',
                'note' => 'Thiết lập giá ban đầu hệ thống',
                'created_at' => $p->created_at ?? now(),
                'updated_at' => $p->created_at ?? now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('price_histories');
    }
};
