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
    Schema::create('cbts', function (Blueprint $table) {
        $table->id();
        $table->enum('language_type', ['single', 'multiple']);
        $table->json('language_ids');
        $table->unsignedBigInteger('category_id');
        $table->unsignedBigInteger('sub_category_id');
        $table->integer('question_limit')->nullable();
        $table->timestamps();

        $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        $table->foreign('sub_category_id')->references('id')->on('sub_categories')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cbts');
    }
};
