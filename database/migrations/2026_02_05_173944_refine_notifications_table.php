<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Truncate to avoid enum data issues during development
        DB::table('notifications')->truncate();

        Schema::table('notifications', function (Blueprint $table) {
            if (Schema::hasColumn('notifications', 'course_id')) {
                $table->dropColumn(['course_id', 'link_title', 'link_url']);
            }
        });

        // Update the enum type using raw SQL
        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('Notification', 'Announcement') NOT NULL DEFAULT 'Announcement'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert enum
        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('All User', 'Akram User') DEFAULT 'All User'");

        Schema::table('notifications', function (Blueprint $table) {
            $table->string('course_id')->nullable();
            $table->string('link_title')->nullable();
            $table->string('link_url')->nullable();
        });
    }
};
