<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->string('ip_address', 45)->nullable()->after('action');   // Ở đâu
            $table->string('user_agent', 255)->nullable()->after('ip_address'); // Thiết bị
            $table->index('timestamp');
        });
    }

    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex(['timestamp']);
            $table->dropColumn(['ip_address', 'user_agent']);
        });
    }
};
