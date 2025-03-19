<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('status')->default(1);
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->enum('mode', ['Regular', 'Upgrade Plan']);
            $table->string('discount');
            $table->string('valid_until'); 
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('offers');
    }
};
