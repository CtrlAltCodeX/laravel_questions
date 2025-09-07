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
    Schema::create('mock_tests', function (Blueprint $table) {
        $table->id();
        $table->foreignId('google_user_id')->constrained('google_users')->onDelete('cascade');
        $table->foreignId('sub_category_id')->constrained('sub_categories')->onDelete('cascade');
        $table->integer('right_answer')->default(0);
        $table->integer('wrong_answer')->default(0);
        $table->integer('attempt')->default(0);
        $table->integer('time_taken')->default(0); // seconds/minutes
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mock_tests');
    }
};
