<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('setting_withdrawal_fees', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount');
            $table->unsignedBigInteger('updated_by');
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
        Schema::dropIfExists('setting_withdrawal_fees');
    }
};
