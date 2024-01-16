<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('category');
            $table->string('transaction_type');
            $table->unsignedBigInteger('from_wallet_id')->nullable();
            $table->unsignedBigInteger('to_wallet_id')->nullable();
            $table->unsignedBigInteger('from_coin_id')->nullable();
            $table->unsignedBigInteger('to_coin_id')->nullable();
            $table->string('transaction_number')->nullable();
            $table->string('to_wallet_address')->nullable();
            $table->string('txn_hash')->nullable();
            $table->double('unit')->nullable();
            $table->double('price_per_unit')->nullable();
            $table->decimal('amount')->nullable();
            $table->decimal('transaction_charges')->nullable();
            $table->decimal('transaction_amount')->nullable();
            $table->string('status')->nullable();
            $table->text('remarks')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade');
            $table->foreign('from_wallet_id')
                ->references('id')
                ->on('wallets')
                ->onUpdate('cascade');
            $table->foreign('to_wallet_id')
                ->references('id')
                ->on('wallets')
                ->onUpdate('cascade');
            $table->foreign('from_coin_id')
                ->references('id')
                ->on('coins')
                ->onUpdate('cascade');
            $table->foreign('to_coin_id')
                ->references('id')
                ->on('coins')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
