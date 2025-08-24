<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_bank_count', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('google_user_id');
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('topic_id');
            $table->integer('count')->default(0);
            $table->timestamps();

            // Foreign Keys (agar tables hain)
            $table->foreign('google_user_id')->references('id')->on('google_users')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('topic_id')->references('id')->on('topics')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_bank_count');
    }
};
