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
        Schema::table('providers', function (Blueprint $table) {
            // Add verification status tracking
            $table->enum('verification_status', ['pending', 'approved', 'rejected'])
                ->default('pending')
                ->after('is_verified')
                ->comment('Provider verification status: pending, approved, or rejected');

            // Add rejection reason for detailed feedback
            $table->text('rejection_reason')
                ->nullable()
                ->after('verification_status')
                ->comment('Reason for rejection if provider is rejected');

            // Add timestamps for audit trail
            $table->timestamp('verified_at')
                ->nullable()
                ->after('rejection_reason')
                ->comment('When the provider was last verified/rejected');

            $table->unsignedBigInteger('verified_by')
                ->nullable()
                ->after('verified_at')
                ->comment('ID of the admin who verified the provider');

            // Add foreign key for verified_by
            $table->foreign('verified_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('providers', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn([
                'verification_status',
                'rejection_reason',
                'verified_at',
                'verified_by',
            ]);
        });
    }
};
