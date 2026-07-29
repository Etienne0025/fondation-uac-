<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reseau_sociaux', function (Blueprint $table) {
            $table->enum('nomcompte', ['gmail', 'whatsapp', 'facebook', 'messenger'])->change();
        });
    }

    public function down(): void
    {
        //
    }
};
