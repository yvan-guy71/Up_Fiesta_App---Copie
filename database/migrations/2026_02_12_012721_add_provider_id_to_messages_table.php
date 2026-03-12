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
        Schema::table('messages', function (Blueprint $table) {
            if (!Schema::hasColumn('messages', 'provider_id')) {
                $table->foreignId('provider_id')->nullable()->constrained('providers')->onDelete('set null');
            }
            if (!Schema::hasColumn('messages', 'deleted_for_sender')) {
                $table->boolean('deleted_for_sender')->default(false);
            }
            if (!Schema::hasColumn('messages', 'deleted_for_receiver')) {
                $table->boolean('deleted_for_receiver')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            if (Schema::hasColumn('messages', 'provider_id')) {
                $table->dropConstrainedForeignId('provider_id');
            }
            foreach (['deleted_for_sender', 'deleted_for_receiver'] as $column) {
                if (Schema::hasColumn('messages', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
