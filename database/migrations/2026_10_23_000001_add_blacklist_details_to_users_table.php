<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Lý do: Bom hàng, Nợ xấu, Gian lận thanh toán, Khác
            $table->string('blacklist_reason', 30)->nullable()->after('is_blacklisted');
            $table->text('blacklist_note')->nullable()->after('blacklist_reason');
            $table->timestamp('blacklisted_at')->nullable()->after('blacklist_note');
            $table->foreignId('blacklisted_by')->nullable()->after('blacklisted_at')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('blacklisted_by');
            $table->dropColumn(['blacklist_reason', 'blacklist_note', 'blacklisted_at']);
        });
    }
};
