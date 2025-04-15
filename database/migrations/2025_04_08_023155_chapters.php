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
        Schema::create('chapters', function (Blueprint $table) {
            $table->id();
            $table->enum('revelation_place',['makkah','madinah']);
            $table->integer("revelation_order");
            $table->boolean('bismillah_pre');
            $table->string('name_simple');
            $table->string('name_arabic');
            $table->integer("verses_count");
            $table->json('pages');
            $table->json('translated_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
