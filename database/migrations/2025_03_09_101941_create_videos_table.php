<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('v_no');
            $table->string('thumbnail');
            $table->unsignedBigInteger('topic_id');
            $table->text('description')->nullable();
            $table->string('youtube_link');
            $table->string('video_id');
            $table->string('duration');
            $table->enum('video_type', ['Free', 'Paid']);
            $table->string('pdf_link')->nullable();
            $table->timestamps();

            // Foreign key for topic_id
            $table->foreign('topic_id')->references('id')->on('topics')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
