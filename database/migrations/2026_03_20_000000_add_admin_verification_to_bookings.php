<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Flag pour indiquer si la notification de notation a été envoyée
            if (! Schema::hasColumn('bookings', 'require_client_review')) {
                $table->boolean('require_client_review')->default(false)->after('provider_done_at');
            }
            // Timestamp de la demande de notation au client
            if (! Schema::hasColumn('bookings', 'client_review_requested_at')) {
                $table->timestamp('client_review_requested_at')->nullable()->after('require_client_review');
            }
            // Statut de vérification par l'admin: pending, verified
            if (! Schema::hasColumn('bookings', 'admin_verification_status')) {
                $table->string('admin_verification_status')->default('pending')->after('client_review_requested_at');
            }
            // Timestamp de vérification par l'admin
            if (! Schema::hasColumn('bookings', 'admin_verified_at')) {
                $table->timestamp('admin_verified_at')->nullable()->after('admin_verification_status');
            }
            // ID de l'admin qui a vérifié
            if (! Schema::hasColumn('bookings', 'admin_verified_by')) {
                $table->foreignId('admin_verified_by')->nullable()->constrained('users')->onDelete('set null')->after('admin_verified_at');
            }
            // Réduction de commission appliquée (-15%)
            if (! Schema::hasColumn('bookings', 'provider_commission_reduction')) {
                $table->decimal('provider_commission_reduction', 12, 2)->default(0)->after('admin_verified_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'provider_commission_reduction')) {
                $table->dropColumn('provider_commission_reduction');
            }
            if (Schema::hasColumn('bookings', 'admin_verified_by')) {
                $table->dropForeign(['admin_verified_by']);
                $table->dropColumn('admin_verified_by');
            }
            if (Schema::hasColumn('bookings', 'admin_verified_at')) {
                $table->dropColumn('admin_verified_at');
            }
            if (Schema::hasColumn('bookings', 'admin_verification_status')) {
                $table->dropColumn('admin_verification_status');
            }
            if (Schema::hasColumn('bookings', 'client_review_requested_at')) {
                $table->dropColumn('client_review_requested_at');
            }
            if (Schema::hasColumn('bookings', 'require_client_review')) {
                $table->dropColumn('require_client_review');
            }
        });
    }
};
