<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table): void {
            $table->string('carrier', 100)->default('GHN Sandbox')->after('order_id');
            $table->string('tracking_url')->nullable()->after('ghn_order_code');
            $table->json('tracking_events')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table): void {
            $table->dropColumn(['carrier', 'tracking_url', 'tracking_events']);
        });
    }
};
