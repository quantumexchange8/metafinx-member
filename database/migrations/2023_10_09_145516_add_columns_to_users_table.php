<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('setting_rank_id')->nullable()->after('status');
            $table->integer('total_affiliate')->nullable()->after('setting_rank_id');
            $table->decimal('self_deposit', 13, 2)->nullable()->after('total_affiliate');
            $table->decimal('valid_affiliate_deposit', 13, 2)->nullable()->after('self_deposit');

            $table->foreign('setting_rank_id')
                ->references('id')
                ->on('setting_ranks')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
