<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('service_request_id')
                ->nullable()
                ->after('provider_id')
                ->comment('Link to the original service request');

            $table->unsignedBigInteger('assigned_service_id')
                ->nullable()
                ->after('service_request_id')
                ->comment('Link to the assigned service that was accepted');

            // Add foreign keys
            $table->foreign('service_request_id')
                ->references('id')
                ->on('service_requests')
                ->nullOnDelete();

            $table->foreign('assigned_service_id')
                ->references('id')
                ->on('assigned_services')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['service_request_id']);
            $table->dropForeign(['assigned_service_id']);
            $table->dropColumn(['service_request_id', 'assigned_service_id']);
        });
    }
};
