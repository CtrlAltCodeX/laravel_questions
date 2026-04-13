<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ranks', function (Blueprint $table) {
            $table->dropForeign(['sub_category_id']);
            $table->dropColumn('sub_category_id');
            $table->foreignId('live_test_id')->after('google_user_id')->constrained('live_tests')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ranks', function (Blueprint $table) {
            $table->dropForeign(['live_test_id']);
            $table->dropColumn('live_test_id');
            $table->foreignId('sub_category_id')->after('google_user_id')->constrained('sub_categories')->onDelete('cascade');
        });
    }
};