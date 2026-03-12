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
            if (!Schema::hasColumn('providers', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            }
            if (Schema::hasColumn('providers', 'city')) {
                $table->dropColumn('city');
            }
            if (!Schema::hasColumn('providers', 'city_id')) {
                $table->foreignId('city_id')->nullable()->constrained()->onDelete('set null');
            }
            if (!Schema::hasColumn('providers', 'email')) {
                $table->string('email')->nullable();
            }
            if (!Schema::hasColumn('providers', 'website')) {
                $table->string('website')->nullable();
            }
            if (!Schema::hasColumn('providers', 'logo')) {
                $table->string('logo')->nullable();
            }
            if (!Schema::hasColumn('providers', 'is_verified')) {
                $table->boolean('is_verified')->default(false);
            }
            if (!Schema::hasColumn('providers', 'base_price')) {
                $table->decimal('base_price', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('providers', 'price_range_max')) {
                $table->decimal('price_range_max', 10, 2)->nullable();
            }
        });

        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'city')) {
                $table->dropColumn('city');
            }
            if (!Schema::hasColumn('events', 'city_id')) {
                $table->foreignId('city_id')->nullable()->constrained()->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('providers', function (Blueprint $table) {
            if (!Schema::hasColumn('providers', 'city')) {
                $table->string('city')->nullable();
            }
            if (Schema::hasColumn('providers', 'city_id')) {
                $table->dropConstrainedForeignId('city_id');
            }
            
            $columnsToDrop = [];
            foreach (['email', 'website', 'logo', 'is_verified', 'base_price', 'price_range_max'] as $column) {
                if (Schema::hasColumn('providers', $column)) {
                    $columnsToDrop[] = $column;
                }
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });

        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'city')) {
                $table->string('city')->nullable();
            }
            if (Schema::hasColumn('events', 'city_id')) {
                $table->dropConstrainedForeignId('city_id');
            }
        });
    }
};
