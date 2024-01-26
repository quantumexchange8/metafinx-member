<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('coin_multi_levels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('sponsor_id')->nullable();
            $table->unsignedBigInteger('upline_id')->nullable();
            $table->string('hierarchy_list')->nullable();
            $table->string('position')->nullable();
            $table->decimal('coin_stacking_amount', 11, 2)->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coin_multi_levels');
    }
};
