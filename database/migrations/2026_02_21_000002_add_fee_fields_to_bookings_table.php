<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('bookings', 'commission_rate')) {
                $table->decimal('commission_rate', 5, 2)->nullable()->after('total_price');
            }

            if (! Schema::hasColumn('bookings', 'platform_fee')) {
                $table->decimal('platform_fee', 12, 2)->default(0)->after('commission_rate');
            }

            if (! Schema::hasColumn('bookings', 'provider_amount')) {
                $table->decimal('provider_amount', 12, 2)->default(0)->after('platform_fee');
            }

            if (! Schema::hasColumn('bookings', 'payout_status')) {
                $table->string('payout_status')->default('pending')->after('payment_method');
            }

            if (! Schema::hasColumn('bookings', 'payout_date')) {
                $table->timestamp('payout_date')->nullable()->after('payout_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'payout_date')) {
                $table->dropColumn('payout_date');
            }
            if (Schema::hasColumn('bookings', 'payout_status')) {
                $table->dropColumn('payout_status');
            }
            if (Schema::hasColumn('bookings', 'provider_amount')) {
                $table->dropColumn('provider_amount');
            }
            if (Schema::hasColumn('bookings', 'platform_fee')) {
                $table->dropColumn('platform_fee');
            }
            if (Schema::hasColumn('bookings', 'commission_rate')) {
                $table->dropColumn('commission_rate');
            }
        });
    }
};

