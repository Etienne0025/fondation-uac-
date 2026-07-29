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
    Schema::create('reseau_sociaux', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->enum('nomcompte', ['gmail', 'whatsapp','facebook','messenger']);
        $table->string('email_compte')->nullable();       // pour gmail
        $table->string('telephone_compte')->nullable();   // pour whatsapp
        $table->string('access_token')->nullable();       // token OAuth Gmail
        $table->string('refresh_token')->nullable();      // refresh token Gmail
        $table->integer('delai_alerte')->default(30);     // délai en minutes
        $table->boolean('actif')->default(true);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reseau_sociaux');
    }
};
