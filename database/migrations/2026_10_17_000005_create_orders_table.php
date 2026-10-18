<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('voucher_code', 30)->nullable();
            $table->foreign('voucher_code')->references('code')->on('vouchers')->nullOnDelete();
            $table->timestamp('order_date')->useCurrent();
            $table->decimal('total_amount', 10, 2);
            $table->decimal('shipping_fee', 10, 2)->default(25000);
            $table->string('status', 50)->default('Chờ xác nhận');

            // Bổ sung so với báo cáo: thông tin người nhận (bắt buộc để tạo vận đơn GHN)
            $table->string('receiver_name', 100);
            $table->string('receiver_phone', 15);
            $table->string('shipping_address', 500);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
