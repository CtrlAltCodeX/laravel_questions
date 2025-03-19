<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('score_boards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('google_user_id');
            $table->unsignedBigInteger('sub_category_id');
            $table->integer('total_videos')->default(0);
            $table->integer('quiz_practice')->default(0);
            $table->integer('test_rank')->default(0);
            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('score_boards');
    }
};
