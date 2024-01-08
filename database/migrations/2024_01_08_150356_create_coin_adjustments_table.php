<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('coin_adjustments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('coin_id');
            $table->unsignedBigInteger('setting_coin_id');
            $table->string('type');
            $table->float('old_unit');
            $table->float('unit');
            $table->float('new_unit');
            $table->text('description');
            $table->unsignedBigInteger('handle_by');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
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
        Schema::dropIfExists('coin_adjustments');
    }
};
