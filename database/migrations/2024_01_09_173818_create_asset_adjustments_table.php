<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('asset_adjustments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('wallet_id')->nullable();
            $table->unsignedBigInteger('coin_id')->nullable();
            $table->unsignedBigInteger('setting_coin_id')->nullable();
            $table->string('type');
            $table->double('old_amount');
            $table->double('amount');
            $table->double('new_amount')->nullable();
            $table->text('description');
            $table->unsignedBigInteger('handle_by');
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
            $table->foreign('coin_id')
                ->references('id')
                ->on('coins')
                ->onUpdate('cascade');
            $table->foreign('setting_coin_id')
                ->references('id')
                ->on('setting_coins')
                ->onUpdate('cascade');
            $table->foreign('handle_by')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_adjustments');
    }
};
