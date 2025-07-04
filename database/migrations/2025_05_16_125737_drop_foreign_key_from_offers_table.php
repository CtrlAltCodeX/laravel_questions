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
    Schema::table('offers', function (Blueprint $table) {
        $table->dropForeign(['subject_id']); // foreign key drop
    });
}

public function down()
{
    Schema::table('offers', function (Blueprint $table) {
        $table->foreign('subject_id')->references('id')->on('subjects'); // rollback ke liye
    });
}

};
