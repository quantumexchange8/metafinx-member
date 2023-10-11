<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('earnings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('upline_id');
            $table->unsignedBigInteger('upline_rank_id');
            $table->unsignedBigInteger('upline_wallet_id');
            $table->unsignedBigInteger('downline_id');
            $table->unsignedBigInteger('downline_rank_id');
            $table->decimal('before_amount', 11, 2)->nullable();
            $table->decimal('percentage', 3, 2)->nullable();
            $table->decimal('after_amount', 11, 2)->nullable();
            $table->string('type')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('upline_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade');
            $table->foreign('upline_rank_id')
                ->references('id')
                ->on('setting_ranks')
                ->onUpdate('cascade');
            $table->foreign('upline_wallet_id')
                ->references('id')
                ->on('wallets')
                ->onUpdate('cascade');
            $table->foreign('downline_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade');
            $table->foreign('downline_rank_id')
                ->references('id')
                ->on('setting_ranks')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('earnings');
    }
};
