<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('google_users', function (Blueprint $table) {
            $table->string('profile_image')->nullable()->after('email');
            $table->string('phone_number')->nullable()->after('profile_image');
            $table->enum('login_type', ['google', 'facebook', 'apple'])->default('google')->after('phone_number');
            $table->integer('coins')->default(0)->after('login_type');
            $table->string('plan')->nullable()->after('coins');
            $table->string('referral_code')->nullable()->unique()->after('plan');
            $table->string('friend_code')->nullable()->after('referral_code');
            $table->enum('status', ['Enabled', 'Disabled'])->default('Enabled')->after('friend_code');
            $table->unsignedBigInteger('category_id')->nullable()->after('status');

        });
    }

    public function down()
    {
        Schema::table('google_users', function (Blueprint $table) {
            $table->dropColumn([
                'profile_image',
                'phone_number',
                'login_type',
                'coins',
                'plan',
                'referral_code',
                'friend_code',
                'status',
                'category_id'
            ]);
        });
    }
};
