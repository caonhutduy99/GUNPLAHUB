<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            // Mỗi đơn hàng thành công có đúng một hóa đơn
            $table->foreignId('order_id')->unique()->constrained('orders')->restrictOnDelete();
            $table->string('invoice_number', 40)->unique(); // Mã số duy nhất, ví dụ HD202610-000001
            $table->timestamp('issued_at');
            $table->decimal('total_amount', 10, 2); // Số tiền tại thời điểm xuất hóa đơn
            $table->timestamps();
        });

        // Xuất hóa đơn cho các đơn đã giao thành công từ trước
        $orders = DB::table('orders')
            ->whereIn('status', ['Đã giao', 'Chờ duyệt trả hàng', 'Đang trả hàng', 'Đã trả hàng/Hoàn tiền'])
            ->get();

        foreach ($orders as $order) {
            $issuedAt = Carbon::parse($order->delivered_at ?? $order->updated_at ?? now());

            $id = DB::table('invoices')->insertGetId([
                'order_id' => $order->id,
                'invoice_number' => (string) Str::uuid(),
                'issued_at' => $issuedAt,
                'total_amount' => $order->total_amount,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('invoices')->where('id', $id)->update([
                'invoice_number' => sprintf('HD%s-%06d', $issuedAt->format('Ym'), $id),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
