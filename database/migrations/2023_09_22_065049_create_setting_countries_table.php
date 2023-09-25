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
        Schema::create('setting_countries', function (Blueprint $table) {
            $table->id();
            $table->string('name_en');
            $table->string('name_zhHans');
            $table->string('name_zhHant');
            $table->string('name_vn');
            $table->string('iso', 10);
            $table->string('phone_code', 20);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting_countries');
    }
};
