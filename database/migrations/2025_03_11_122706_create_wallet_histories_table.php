<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('wallet_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('google_user_id');
            $table->integer('coin');
            $table->string('method');
            $table->dateTime('date');
            $table->string('transaction_id')->unique();
            $table->decimal('amount', 10, 2);
            $table->timestamps();

            // Update foreign key reference
            $table->foreign('google_user_id')->references('id')->on('google_users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('wallet_histories');
    }
};
