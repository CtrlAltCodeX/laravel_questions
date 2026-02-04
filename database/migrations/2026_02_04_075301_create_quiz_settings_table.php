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
        Schema::create('quiz_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('course_id');
            $table->integer('quiz_limit')->default(20);
            $table->integer('timer')->default(30);
            $table->boolean('auto_next')->default(true);
            $table->boolean('sound')->default(false);
            $table->boolean('shuffle')->default(true);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('google_users')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_settings');
    }
};
