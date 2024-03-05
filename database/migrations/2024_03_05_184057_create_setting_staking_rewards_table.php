<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('setting_staking_rewards', function (Blueprint $table) {
            $table->id();
            $table->string('month')->nullable();
            $table->date('release_date')->nullable();
            $table->decimal('percent')->nullable();
            $table->boolean('is_done')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setting_staking_rewards');
    }
};
