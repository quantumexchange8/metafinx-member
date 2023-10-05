<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('investment_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('subscription_id');
            $table->unsignedBigInteger('investment_plan_id');
            $table->unsignedBigInteger('wallet_id');
            $table->decimal('amount', 11, 2);
            $table->decimal('total_earning', 11, 2)->nullable();
            $table->string('status')->default('CoolingPeriod');
            $table->date('next_roi_date')->nullable();
            $table->date('expired_date')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade');
            $table->foreign('investment_plan_id')
                ->references('id')
                ->on('investment_plans')
                ->onUpdate('cascade');
            $table->foreign('wallet_id')
                ->references('id')
                ->on('wallets')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investment_subscriptions');
    }
};
