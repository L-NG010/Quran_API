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
        Schema::create('juzs', function (Blueprint $table) {
            $table->id();
            $table->integer('juz_number');
            $table->json('verse_mapping');
            $table->integer('first_verse_id');
            $table->integer('last_verse_id');
            $table->integer('verses_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('juzs');
    }
};
