<?php
// database/migrations/2026_07_20_000001_add_facebook_fields_to_reseau_sociaux_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reseau_sociaux', function (Blueprint $table) {
            $table->string('page_id')->nullable()->after('nomcompte');
            $table->string('nom_page')->nullable()->after('page_id');
        });
    }

    public function down()
    {
        Schema::table('reseau_sociaux', function (Blueprint $table) {
            $table->dropColumn(['page_id', 'nom_page']);
        });
    }
};