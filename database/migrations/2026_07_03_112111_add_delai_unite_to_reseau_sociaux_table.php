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
    Schema::table('reseau_sociaux', function (Blueprint $table) {
        $table->string('delai_unite')->default('minutes')->after('delai_alerte');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reseau_sociaux', function (Blueprint $table) {
            //
        });
    }
};
