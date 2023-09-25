<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('country', 100);
            $table->string('phone')->unique();
            $table->text('address_1')->nullable();
            $table->text('address_2')->nullable();
            $table->string('verification_type', 20)->nullable();
            $table->decimal('cash_wallet', 11, 2)->default(0.00);
            $table->string('kyc_approval')->default('pending');
            $table->text('kyc_approval_description')->nullable();
            $table->unsignedBigInteger('upline_id')->nullable();
            $table->string('hierarchyList')->nullable();
            $table->string('referral_code', 20)->nullable();
            $table->integer('status')->default(1);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
