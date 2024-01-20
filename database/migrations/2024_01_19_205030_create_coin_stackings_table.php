<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('coin_stackings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('coin_id');
            $table->unsignedBigInteger('transaction_id');
            $table->unsignedBigInteger('investment_plan_id');
            $table->string('subscription_number')->nullable();
            $table->string('type')->nullable();
            $table->decimal('stacking_unit', 11, 4)->nullable();
            $table->decimal('stacking_price', 11, 2)->nullable();
            $table->decimal('stacking_fee', 11, 2)->nullable();
            $table->decimal('total_earning', 11, 4)->nullable();
            $table->string('status')->default('OnGoingPeriod');
            $table->text('remarks')->nullable();
            $table->date('next_roi_date')->nullable();
            $table->date('expired_date')->nullable();
            $table->date('terminated_date')->nullable();
            $table->decimal('max_capped_price')->nullable();
            $table->integer('reinvest_number')->default(0);
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
            $table->foreign('transaction_id')
                ->references('id')
                ->on('transactions')
                ->onUpdate('cascade');
            $table->foreign('investment_plan_id')
                ->references('id')
                ->on('investment_plans')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coin_stackings');
    }
};
