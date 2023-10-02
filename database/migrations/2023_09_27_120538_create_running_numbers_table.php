<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('running_numbers', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('prefix');
            $table->integer('digits');
            $table->integer('last_number');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('running_numbers');
    }
};
