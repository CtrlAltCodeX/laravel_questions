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
        Schema::table('questions', function (Blueprint $table) {
            $table->foreignId(
                'language_id'
            )->constrained()->onDelete('cascade');
            $table-> foreignId(
                'category_id'
            )->constrained()->onDelete('cascade');
            $table->foreignId(
                'sub_category_id'
            )->constrained()->onDelete('cascade');
            $table->foreignId(
                'subject_id'
            )->constrained()->onDelete('cascade');
            $table->foreignId(
                'topic_id'
            )->constrained()->onDelete('cascade');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['language_id']);
            $table->dropForeign(['category_id']);
            $table->dropForeign(['sub_category_id']);
            $table->dropForeign(['subject_id']);
            $table->dropForeign(['topic_id']);
        });
    }
};