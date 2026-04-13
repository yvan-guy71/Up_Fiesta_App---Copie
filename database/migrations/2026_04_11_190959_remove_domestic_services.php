<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all domestic service categories
        $domesticCategories = DB::table('service_categories')
            ->where('kind', 'domestiques')
            ->pluck('id');

        if ($domesticCategories->isNotEmpty()) {
            // Delete bookings associated with domestic categories
            DB::table('bookings')
                ->whereIn('category_id', $domesticCategories)
                ->delete();

            // Delete service requests associated with domestic categories
            DB::table('service_requests')
                ->whereIn('category_id', $domesticCategories)
                ->delete();

            // Delete assigned services associated with domestic categories  
            DB::table('assigned_services')
                ->whereIn('category_id', $domesticCategories)
                ->delete();

            // Delete the domestic categories
            DB::table('service_categories')
                ->whereIn('id', $domesticCategories)
                ->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a feature removal, cannot be reversed safely
    }
};
