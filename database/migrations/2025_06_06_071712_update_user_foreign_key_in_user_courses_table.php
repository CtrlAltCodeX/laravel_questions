<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUserForeignKeyInUserCoursesTable extends Migration
{
    public function up()
    {
        Schema::table('user_courses', function (Blueprint $table) {
            // Drop old foreign key
            $table->dropForeign(['user_id']);

            // Add new foreign key to google_users table
            $table->foreign('user_id')->references('id')->on('google_users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('user_courses', function (Blueprint $table) {
            // Drop new foreign key
            $table->dropForeign(['user_id']);

            // Restore original foreign key to users table
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
}
