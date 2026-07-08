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
    Schema::create('alertes_reseau', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('reseau_social_id')->constrained('reseau_sociaux')->onDelete('cascade');
        $table->enum('type', ['gmail', 'whatsapp']);
        $table->string('expediteur')->nullable();
        $table->string('sujet')->nullable();
        $table->integer('messages_non_lus')->default(0);
        $table->boolean('vue')->default(false);
        $table->timestamp('detecte_a');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alertes_reseau');
    }
};
