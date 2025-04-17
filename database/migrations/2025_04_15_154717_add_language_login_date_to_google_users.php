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
        Schema::table('google_users', function (Blueprint $table) {
            $table->unsignedBigInteger('language_id')->nullable()->after('category_id');
        $table->dateTime('login_date')->nullable()->after('language_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('google_users', function (Blueprint $table) {
            $table->dropColumn(['language_id', 'login_date']);
                });
    }
};
