<?php
// database/migrations/xxxx_xx_xx_add_subscription_fields_to_user_courses_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_courses', function (Blueprint $table) {
            $table->string('subscription_type')->nullable()->after('course_id');
            $table->date('valid_from')->nullable()->after('subscription_type');
            $table->date('valid_to')->nullable()->after('valid_from');
        });
    }

    public function down(): void
    {
        Schema::table('user_courses', function (Blueprint $table) {
            $table->dropColumn(['subscription_type', 'valid_from', 'valid_to']);
        });
    }
};
