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
        Schema::create('script_text', function (Blueprint $table) {
            $table->id();
            $table->string('verse_key', 10)->unique();
            $table->text('text_imlaei');
            $table->text('text_indopak');
            $table->text('text_uthmani');
            $table->text('text_uthmani_tajweed');
            $table->text('text_uthmani_simple');
            $table->text('code_v1');
            $table->text('code_v2');
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
