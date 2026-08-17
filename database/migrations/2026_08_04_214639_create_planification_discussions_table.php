<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('planification_discussions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membre_id')->constrained('users')->cascadeOnDelete(); // la ligne concernee
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();    // l'auteur du message
            $table->date('semaine_debut'); // lundi de la semaine concernee
            $table->text('message');
            $table->timestamps();

            $table->index(['membre_id', 'semaine_debut']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('planification_discussions');
    }
};