<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('planification_observations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('semaine_debut'); // lundi de la semaine concernee
            $table->text('observation_admin')->nullable();
            $table->text('observation_personnelle')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'semaine_debut']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('planification_observations');
    }
};