<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('setting_ranks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('self_deposit', 11, 2);
            $table->integer('valid_direct_referral')->nullable();
            $table->decimal('valid_affiliate_deposit', 11, 2)->nullable();
            $table->decimal('capping_per_line', 11, 2)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setting_ranks');
    }
};
