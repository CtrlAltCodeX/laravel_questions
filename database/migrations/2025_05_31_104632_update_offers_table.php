<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::table('offers', function (Blueprint $table) {
        // Remove old fields
        $table->dropColumn(['language_id', 'category_id', 'sub_category_id', 'subject_id', 'mode', 'valid_until']);

        // Add new fields
        $table->json('course')->nullable();
        $table->json('subscription')->nullable();
        $table->string('upgrade')->nullable();

        // Valid till (from and to)
        $table->date('valid_from')->nullable();
        $table->date('valid_to')->nullable();
    });
}

public function down()
{
    Schema::table('offers', function (Blueprint $table) {
        // Revert changes
        $table->unsignedBigInteger('language_id')->nullable();
        $table->unsignedBigInteger('category_id')->nullable();
        $table->unsignedBigInteger('sub_category_id')->nullable();
        $table->unsignedBigInteger('subject_id')->nullable();
        $table->string('mode')->nullable();
        $table->date('valid_until')->nullable();

        $table->dropColumn(['course', 'subscription', 'upgrade', 'valid_from', 'valid_to']);
    });
}

};
