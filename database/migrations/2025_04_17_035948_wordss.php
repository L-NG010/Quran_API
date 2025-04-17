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
        Schema::create('words', function (Blueprint $table) {
            $table->id();
            $table->integer('original_id'); // dari "id" asli word
            $table->integer('position');
            $table->string('char_type_name');
            $table->string('location');
            $table->string('text_uthmani');
            $table->integer('page_number');
            $table->integer('line_number');
            $table->string('text');

            // Translation subfield
            $table->string('translation_text');
            $table->string('translation_language_name');

            // Transliteration subfield (nullable karena bisa null)
            $table->string('transliteration_text')->nullable();
            $table->string('transliteration_language_name')->nullable();

            $table->timestamps();
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
