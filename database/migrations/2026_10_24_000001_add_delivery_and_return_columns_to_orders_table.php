<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Thời điểm giao thành công: dùng tính hạn trả hàng 7 ngày và thống kê doanh thu
            $table->timestamp('delivered_at')->nullable()->after('order_date');
            // Ảnh minh chứng khi khách yêu cầu trả hàng
            $table->string('return_image')->nullable()->after('return_reason');
        });

        // Các đơn đã giao từ trước: lấy thời điểm cập nhật gần nhất làm ngày giao
        DB::table('orders')
            ->whereIn('status', ['Đã giao', 'Chờ duyệt trả hàng', 'Đang trả hàng', 'Đã trả hàng/Hoàn tiền'])
            ->whereNull('delivered_at')
            ->update(['delivered_at' => DB::raw('updated_at')]);
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['delivered_at', 'return_image']);
        });
    }
};
