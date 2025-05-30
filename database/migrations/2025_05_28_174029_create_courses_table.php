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
    Schema::create('courses', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->unsignedBigInteger('language_id');
        $table->unsignedBigInteger('category_id');
        $table->unsignedBigInteger('sub_category_id');
        $table->unsignedBigInteger('subject_id');
        $table->boolean('status')->default(1); // active/inactive
        $table->json('subscription'); // JSON field for subscription plans
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
