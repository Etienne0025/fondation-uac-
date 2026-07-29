<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acces_activite_utilisateur', function (Blueprint $table) {
            $table->id();
            $table->foreignId('acces_activite_id')->constrained('acces_activites')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['acces_activite_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acces_activite_utilisateur');
    }
};