<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
 public function up()
{
    Schema::table('payments', function (Blueprint $table) {
        $table->unsignedBigInteger('user_id')->nullable()->after('contact');
        $table->unsignedBigInteger('course_id')->nullable()->after('user_id');

        // Optional: Add foreign key constraints
        // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        // $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
    });
}

public function down()
{
    Schema::table('payments', function (Blueprint $table) {
        $table->dropColumn(['user_id', 'course_id']);
    });
}

};
