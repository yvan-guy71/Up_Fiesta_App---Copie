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
        // Drop mentorship_requests table
        Schema::dropIfExists('mentorship_requests');
        
        // Drop mentorship columns from providers
        if (Schema::hasColumn('providers', 'is_mentor')) {
            Schema::table('providers', function (Blueprint $table) {
                $table->dropColumn(['is_mentor', 'mentorship_description']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a feature removal, so no down migration
    }
};
