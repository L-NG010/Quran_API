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
        Schema::create('indopak_scripts', function (Blueprint $table) {
            $table->id();
            $table->string('verse_key', 10)->unique();
            $table->text('text_indopak');
            $table->timestamps();

            $table->index('verse_key');
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
