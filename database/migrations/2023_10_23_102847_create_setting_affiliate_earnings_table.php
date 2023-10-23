<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('setting_affiliate_earnings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('setting_rank_id');
            $table->string('name');
            $table->decimal('value');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('setting_rank_id')
                ->references('id')
                ->on('setting_ranks')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setting_affiliate_earnings');
    }
};
