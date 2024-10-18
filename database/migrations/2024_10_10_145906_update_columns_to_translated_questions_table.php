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
        Schema::table('translated_questions', function (Blueprint $table) {
            //
            $table->foreignId('question_id')->nullable()->change();
            $table->foreignId('language_id')->nullable() ->change();
            $table->string('question_text')->nullable() ->change();
            $table->string('option_a')->nullable() ->change();
            $table->string('option_b')->nullable() ->change();
            $table->string('option_c')->nullable() ->change();
            $table->string('option_d')->nullable() ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('translated_questions', function (Blueprint $table) {
            //
        });
    }
};
