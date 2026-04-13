<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Ajouter le champ rejection_reason pour stocker le motif de refus
            $table->text('rejection_reason')->nullable()->after('status');
            
            // Ajouter un champ provider_response_at pour tracker quand le prestataire a répondu
            $table->timestamp('provider_response_at')->nullable()->after('rejection_reason');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['rejection_reason', 'provider_response_at']);
        });
    }
};
