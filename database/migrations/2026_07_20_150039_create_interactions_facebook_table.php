<?php
// database/migrations/2026_07_20_000002_create_interactions_facebook_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('interactions_facebook', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reseau_social_id')->constrained('reseau_sociaux')->cascadeOnDelete();
            $table->string('fb_object_id')->unique();
            $table->enum('type', ['messenger', 'facebook']);
            $table->string('expediteur')->nullable();
            $table->text('contenu')->nullable();
            $table->timestamp('recu_a');
            $table->timestamp('repondu_a')->nullable();
            $table->timestamp('alerte_envoyee_a')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('interactions_facebook');
    }
};