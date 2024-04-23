<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('payment_account_name')->nullable();
            $table->string('payment_platform')->nullable();
            $table->string('payment_platform_name')->nullable();
            $table->string('account_no')->nullable();
            $table->text('bank_branch_address')->nullable();
            $table->string('bank_swift_code')->nullable();
            $table->string('bank_code')->nullable();
            $table->string('bank_code_type')->nullable();
            $table->string('country')->nullable();
            $table->string('currency')->nullable();
            $table->string('status')->default('Active');
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
        Schema::dropIfExists('payment_accounts');
    }
};
