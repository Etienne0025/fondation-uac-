<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tdrs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // créateur (staff)
            $table->string('titre'); // thème publié
            $table->text('description')->nullable();

            // Fichier joint, ajoute par un administrateur/responsable (peut etre null au depart)
            $table->string('fichier_nom')->nullable();
            $table->string('fichier_chemin')->nullable();
            $table->string('fichier_type')->nullable(); // pdf ou word
            $table->foreignId('soumis_par_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('fichier_soumis_a')->nullable();

            $table->string('statut')->default('en_attente'); // en_attente | approuve
            $table->foreignId('approuve_par_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approuve_a')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tdrs');
    }
};