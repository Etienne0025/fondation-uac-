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
    Schema::table('activites', function (Blueprint $table) {
        $table->json('rappels_avant')->nullable()->after('statut');  // ex: ["24h", "1h"]
        $table->json('alertes_apres')->nullable()->after('rappels_avant'); // ex: ["1h", "24h"]
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activites', function (Blueprint $table) {
            //
        });
    }
};
