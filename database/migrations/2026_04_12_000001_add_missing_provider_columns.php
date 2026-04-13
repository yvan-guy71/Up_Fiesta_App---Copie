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
            if (!Schema::hasColumn('providers', 'cni_number')) {
                $table->string('cni_number')->nullable()->unique();
            }

            if (!Schema::hasColumn('providers', 'years_of_experience')) {
                $table->integer('years_of_experience')->nullable();
            }

            if (!Schema::hasColumn('providers', 'is_company')) {
                $table->boolean('is_company')->default(false);
            }

            if (!Schema::hasColumn('providers', 'company_registration_number')) {
                $table->string('company_registration_number')->nullable();
            }

            if (!Schema::hasColumn('providers', 'price_change_status')) {
                $table->string('price_change_status')->default('none'); // 'none', 'pending', 'rejected'
            }

            if (!Schema::hasColumn('providers', 'pending_base_price')) {
                $table->decimal('pending_base_price', 10, 2)->nullable();
            }

            if (!Schema::hasColumn('providers', 'pending_price_range_max')) {
                $table->decimal('pending_price_range_max', 10, 2)->nullable();
            }

            if (!Schema::hasColumn('providers', 'verification_status')) {
                $table->string('verification_status')->default('pending'); // 'pending', 'approved', 'rejected'
            }

            if (!Schema::hasColumn('providers', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable();
            }

            if (!Schema::hasColumn('providers', 'verified_at')) {
                $table->timestamp('verified_at')->nullable();
            }

            if (!Schema::hasColumn('providers', 'verified_by')) {
                $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('providers', function (Blueprint $table) {
            $columnsToDrop = [
                'cni_number',
                'years_of_experience',
                'is_company',
                'company_registration_number',
                'price_change_status',
                'pending_base_price',
                'pending_price_range_max',
                'verification_status',
                'rejection_reason',
                'verified_at',
            ];

            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('providers', $column)) {
                    $table->dropColumn($column);
                }
            }

            if (Schema::hasColumn('providers', 'verified_by')) {
                $table->dropConstrainedForeignId('verified_by');
            }
        });
    }
};
