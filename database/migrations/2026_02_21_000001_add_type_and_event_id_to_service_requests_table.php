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
        Schema::table('service_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('service_requests', 'type')) {
                $table->string('type')->default('service')->after('user_id');
            }

            if (! Schema::hasColumn('service_requests', 'event_id')) {
                $table->foreignId('event_id')
                    ->nullable()
                    ->after('provider_id')
                    ->constrained('events')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            if (Schema::hasColumn('service_requests', 'event_id')) {
                $table->dropConstrainedForeignId('event_id');
            }

            if (Schema::hasColumn('service_requests', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};

