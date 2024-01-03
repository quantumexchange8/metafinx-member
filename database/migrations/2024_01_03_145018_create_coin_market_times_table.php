<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('coin_market_times', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('setting_coin_id');
            $table->unsignedBigInteger('updated_by');
            $table->time('open_time');
            $table->time('close_time');
            $table->string('frequency_type');
            $table->json('frequency');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade');
            $table->foreign('setting_coin_id')
                ->references('id')
                ->on('setting_coins')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coin_market_times');
    }
};
