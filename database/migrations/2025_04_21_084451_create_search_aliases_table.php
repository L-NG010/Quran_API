<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSearchAliasesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('search_aliases', function (Blueprint $table) {
            $table->id();
            $table->string('keyword'); // Kata kunci yang typo, alternatif, nama populer, dll
            $table->enum('type', ['surat', 'ayat', 'juz', 'halaman']); // Jenis entity
            $table->string('reference_id'); // Misalnya: 2 (untuk surat), 2:255 (untuk ayat), dst
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_aliases');
    }
}
