<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('service_categories', function (Blueprint $table) {
            $table->string('kind')->default('prestations')->after('slug');
        });

        // assign a default value to existing records and optionally mark some as domestiques
        DB::table('service_categories')->update(['kind' => 'prestations']);

        // if you want to classify some known domestic categories automatically, you can uncomment and adjust the
        // list below. otherwise the administrator can update them manually via the Filament interface.
        
        $domestic = [
            'Maçonnerie',
            'Menuiserie',
            'Cuisinier à domicile',
            'Plomberie',
            'Électricité',
            'Peinture',
            'Climatisation',
            'Entretien & Nettoyage',
            'Mécanique',
            'Transport & Logistique',
        ];
        DB::table('service_categories')
            ->whereIn('name', $domestic)
            ->update(['kind' => 'domestiques']);
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_categories', function (Blueprint $table) {
            $table->dropColumn('kind');
        });
    }
};