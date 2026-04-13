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
            if (!Schema::hasColumn('service_requests', 'user_id')) {
                $table->foreignId('user_id')
                    ->nullable()
                    ->constrained('users')
                    ->cascadeOnDelete();
            }

            if (!Schema::hasColumn('service_requests', 'provider_id')) {
                $table->foreignId('provider_id')
                    ->nullable()
                    ->constrained('providers')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('service_requests', 'subject')) {
                $table->string('subject')->nullable();
            }

            if (!Schema::hasColumn('service_requests', 'description')) {
                $table->longText('description')->nullable();
            }

            if (!Schema::hasColumn('service_requests', 'status')) {
                $table->string('status')->default('pending');
            }

            if (!Schema::hasColumn('service_requests', 'kind')) {
                $table->string('kind')->default('prestations');
            }

            if (!Schema::hasColumn('service_requests', 'event_date')) {
                $table->dateTime('event_date')->nullable();
            }

            if (!Schema::hasColumn('service_requests', 'location')) {
                $table->string('location')->nullable();
            }

            if (!Schema::hasColumn('service_requests', 'budget')) {
                $table->decimal('budget', 12, 2)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            if (Schema::hasColumn('service_requests', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }

            if (Schema::hasColumn('service_requests', 'provider_id')) {
                $table->dropConstrainedForeignId('provider_id');
            }

            if (Schema::hasColumn('service_requests', 'subject')) {
                $table->dropColumn('subject');
            }

            if (Schema::hasColumn('service_requests', 'description')) {
                $table->dropColumn('description');
            }

            if (Schema::hasColumn('service_requests', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('service_requests', 'kind')) {
                $table->dropColumn('kind');
            }

            if (Schema::hasColumn('service_requests', 'event_date')) {
                $table->dropColumn('event_date');
            }

            if (Schema::hasColumn('service_requests', 'location')) {
                $table->dropColumn('location');
            }

            if (Schema::hasColumn('service_requests', 'budget')) {
                $table->dropColumn('budget');
            }
        });
    }
};
