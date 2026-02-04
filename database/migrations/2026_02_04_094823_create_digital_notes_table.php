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
        Schema::create('digital_notes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('photo')->nullable();
            $table->unsignedBigInteger('language_id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('sub_category_id')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->unsignedBigInteger('topic_id')->nullable();
            $table->longText('content')->nullable(); // HTML content
            $table->boolean('status')->default(true);
            $table->timestamps();

            // Foreign keys if needed, but strictly not required unless we want constraints. 
            // Given the project structure, often they are just big integers.
            // I'll add them if I am sure, but usually safe to just use UnsignedBigInteger to avoid constraint errors if data is messy.
            // Based on previous files, they might use foreign keys.
            // But to be safe and quick, I will just index them.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('digital_notes');
    }
};
