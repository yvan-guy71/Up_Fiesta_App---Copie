<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('payment_status')->default('unpaid')->after('status'); // unpaid, paid, failed
            $table->string('transaction_id')->nullable()->after('payment_status');
            $table->string('payment_method')->nullable()->after('transaction_id'); // tmoney, flooz, card
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'transaction_id', 'payment_method']);
        });
    }
};
