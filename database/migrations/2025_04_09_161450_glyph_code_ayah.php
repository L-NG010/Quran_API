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
        Schema::create('glyph_code_ayah_v1',function(Blueprint $t){
            $t->id();
            $t->string('verse_key')->unique();
            $t->string('code_v1');
            $t->integer('v1_page');
        });

        Schema::create('glyph_code_ayah_v2',function(Blueprint $t){
            $t->id();
            $t->string('verse_key')->unique();
            $t->string('code_v2');
            $t->integer('v2_page');
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
