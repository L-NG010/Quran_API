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
        Schema::create('verses', function (Blueprint $table) {
            $table->id();
            $table->integer('verse_number');
            $table->string('verse_key');
            $table->integer('hizb_number');
            $table->integer('rub_el_hizb_number');
            $table->integer('ruku_number');
            $table->integer('manzil_number');
            $table->boolean('sajdah_number');
            $table->integer('page_number');
            $table->integer('juz_number');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verses');
    }
};
