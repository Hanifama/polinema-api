<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voucher_categories', function (Blueprint $table) {
            $table->string('vocat_id', 50)->primary();
            $table->string('name'); 
            $table->string('icon', 100)->nullable();
            $table->text('description')->nullable();
            $table->dateTime('created_dt')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voucher_categories');
    }
};
