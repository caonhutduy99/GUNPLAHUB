<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->text('cancel_reason')->nullable()->after('status');
            // Trạng thái trước khi gửi yêu cầu hủy, để Admin từ chối thì đơn quay lại đúng chỗ cũ
            $table->string('status_before_cancel', 50)->nullable()->after('cancel_reason');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['cancel_reason', 'status_before_cancel']);
        });
    }
};
