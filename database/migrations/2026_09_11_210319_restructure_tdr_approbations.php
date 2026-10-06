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
        // 1. Restructurer la table tdrs
        Schema::table('tdrs', function (Blueprint $table) {
            $table->dropForeign(['approuve_par_id']);
            $table->dropColumn(['statut', 'approuve_par_id', 'approuve_a']);
        });

        // 2. Créer la table tdr_approbations si elle n'existe pas encore
        if (!Schema::hasTable('tdr_approbations')) {
            Schema::create('tdr_approbations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tdr_id')->constrained('tdrs')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('statut')->default('approuve');
                $table->text('commentaire')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprimer la table d'approbations
        Schema::dropIfExists('tdr_approbations');

        // Réinstaller les anciennes colonnes dans tdrs
        Schema::table('tdrs', function (Blueprint $table) {
            $table->string('statut')->nullable();
            $table->foreignId('approuve_par_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approuve_a')->nullable();
        });
    }
};