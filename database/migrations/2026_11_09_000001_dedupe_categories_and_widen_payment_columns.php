<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Gộp các danh mục bị trùng tên (do seeder chạy sau migration) rồi đặt ràng buộc duy nhất.
        $duplicates = DB::table('categories')
            ->select('name', DB::raw('MIN(id) as keep_id'))
            ->groupBy('name')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            $extraIds = DB::table('categories')
                ->where('name', $duplicate->name)
                ->where('id', '!=', $duplicate->keep_id)
                ->pluck('id');

            DB::table('products')->whereIn('category_id', $extraIds)->update(['category_id' => $duplicate->keep_id]);
            DB::table('categories')->whereIn('id', $extraIds)->delete();
        }

        Schema::table('categories', function (Blueprint $table) {
            $table->unique('name');
        });

        // 2. Đổi payment_method/status từ ENUM sang chuỗi để thêm trạng thái hoàn tiền
        //    ("Chờ hoàn tiền", "Đã hoàn tiền") và bỏ phương thức MOMO không còn dùng.
        //    Giá trị hợp lệ do ứng dụng kiểm soát (Rule::in và hằng số trong model Payment).
        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_method', 20)->change();
            $table->string('status', 30)->default('Chờ xử lý')->change();
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique(['name']);
        });

        // Không khôi phục ENUM cũ: dữ liệu "Chờ hoàn tiền"/"Đã hoàn tiền" sẽ không còn hợp lệ.
    }
};
