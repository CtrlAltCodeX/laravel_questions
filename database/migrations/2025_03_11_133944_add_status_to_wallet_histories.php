<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('wallet_histories', function (Blueprint $table) {
            $table->string('status')->after('transaction_id'); // Add status column
        });
    }

    public function down()
    {
        Schema::table('wallet_histories', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
