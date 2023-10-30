<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('balance_adjustments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('wallet_id');
            $table->unsignedBigInteger('to_user_id')->nullable();
            $table->unsignedBigInteger('to_wallet_id')->nullable();
            $table->string('type');
            $table->decimal('old_balance');
            $table->decimal('amount');
            $table->decimal('new_balance');
            $table->text('description');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('balance_adjustments');
    }
};
