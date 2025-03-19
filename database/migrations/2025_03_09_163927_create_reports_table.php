<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title');
            $table->enum('type', ['Video', 'Question']);
            $table->unsignedBigInteger('question_id')->nullable();
            $table->unsignedBigInteger('video_id')->nullable();
            $table->text('message');
            $table->dateTime('date');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reports');
    }
};
