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
        Schema::table('question_banks', function (Blueprint $table) {
            $table->dropColumn('question');
            $table->dropColumn('option_a');
            $table->dropColumn('option_b');
            $table->dropColumn('option_c');
            $table->dropColumn('option_d');
            $table->dropColumn('answer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('question_banks', function (Blueprint $table) {
            //
        });
    }
};
