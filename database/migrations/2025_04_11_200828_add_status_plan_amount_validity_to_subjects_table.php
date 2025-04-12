<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->enum('status', ['enabled', 'disabled'])->default('enabled');
            $table->enum('plan', ['Free', 'Paid'])->default('Free');
            $table->string('amount')->nullable();
            $table->string('validity')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn(['status', 'plan', 'amount', 'validity']);
        });
    }
};
