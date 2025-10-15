<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
 public function up()
{
    Schema::table('mock_tests', function (Blueprint $table) {
        $table->integer('total_questions')->after('wrong_answer')->default(0);
    });
}

public function down()
{
    Schema::table('mock_tests', function (Blueprint $table) {
        $table->dropColumn('total_questions');
    });
}

};
