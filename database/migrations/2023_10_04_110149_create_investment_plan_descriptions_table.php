<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('investment_plan_descriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('investment_plan_id');
            $table->string('description');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('investment_plan_id')
                ->references('id')
                ->on('investment_plans')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investment_plan_descriptions');
    }
};
