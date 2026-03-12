<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('bookings', 'provider_done')) {
                $table->boolean('provider_done')->default(false)->after('payout_date');
            }
            if (! Schema::hasColumn('bookings', 'provider_done_at')) {
                $table->timestamp('provider_done_at')->nullable()->after('provider_done');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'provider_done_at')) {
                $table->dropColumn('provider_done_at');
            }
            if (Schema::hasColumn('bookings', 'provider_done')) {
                $table->dropColumn('provider_done');
            }
        });
    }
};
