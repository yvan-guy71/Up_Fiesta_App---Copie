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
            // Renommer les colonnes existantes pour plus de clarté
            if (Schema::hasColumn('providers', 'cni_photo')) {
                $table->renameColumn('cni_photo', 'cni_photo_front');
            }
            if (Schema::hasColumn('providers', 'company_proof_doc')) {
                $table->renameColumn('company_proof_doc', 'company_proof_doc_front');
            }

            // Ajouter les colonnes pour le verso
            if (! Schema::hasColumn('providers', 'cni_photo_front')) {
                $table->string('cni_photo_front')->nullable();
            }
            if (! Schema::hasColumn('providers', 'cni_photo_back')) {
                $table->string('cni_photo_back')->nullable();
            }
            if (! Schema::hasColumn('providers', 'company_proof_doc_front')) {
                $table->string('company_proof_doc_front')->nullable();
            }
            if (! Schema::hasColumn('providers', 'company_proof_doc_back')) {
                $table->string('company_proof_doc_back')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('providers', function (Blueprint $table) {
            if (Schema::hasColumn('providers', 'cni_photo_front')) {
                $table->renameColumn('cni_photo_front', 'cni_photo');
            }
            if (Schema::hasColumn('providers', 'company_proof_doc_front')) {
                $table->renameColumn('company_proof_doc_front', 'company_proof_doc');
            }

            $table->dropColumn(['cni_photo_back', 'company_proof_doc_back']);
        });
    }
};
