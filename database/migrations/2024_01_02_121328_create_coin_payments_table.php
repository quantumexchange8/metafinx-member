<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('coin_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('wallet_id');
            $table->unsignedBigInteger('setting_coin_id');
            $table->string('transaction_id');
            $table->double('unit');
            $table->double('price');
            $table->double('amount');
            $table->double('conversion_rate');
            $table->string('type');
            $table->string('status')->nullable()->default('Success');
            $table->text('remarks')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade');
            $table->foreign('wallet_id')
                ->references('id')
                ->on('wallets')
                ->onUpdate('cascade');
            $table->foreign('setting_coin_id')
                ->references('id')
                ->on('setting_coins')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coin_payments');
    }
};
