<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('wallet_histories', function (Blueprint $table) {
            $table->enum('payment_type', ['credit', 'debit'])->after('amount');
        });
    }

    public function down() {
        Schema::table('wallet_histories', function (Blueprint $table) {
            $table->dropColumn('payment_type');
        });
    }
};
