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
    Schema::table('notifications', function (Blueprint $table) {
        $table->integer('intervalle_valeur')->nullable()->after('envoye');
        $table->string('intervalle_unite')->nullable()->after('intervalle_valeur');
        $table->boolean('repetition_active')->default(false)->after('intervalle_unite');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['intervalle_valeur', 'intervalle_unite', 'repetition_active']);
        });
    }
};
