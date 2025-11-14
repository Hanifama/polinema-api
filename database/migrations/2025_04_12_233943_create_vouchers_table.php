<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->string('voucher_id', 50)->primary();
            $table->string('tenant_id', 50);
            $table->string('title', 255)->nullable();
            $table->text('description')->nullable();
            $table->string('banner_1', 255)->nullable();
            $table->string('banner_2', 255)->nullable();
            $table->string('banner_3', 255)->nullable();
            $table->string('banner_4', 255)->nullable();
            $table->string('discount_type', 50)->nullable(); 
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->decimal('minimum_amount', 10, 2)->nullable();
            $table->decimal('maximum_discount', 10, 2)->nullable();
            $table->dateTime('start_dt')->nullable();
            $table->dateTime('end_dt')->nullable();
            $table->boolean('is_claimed')->default(false);
            $table->integer('quota')->default(0);
            $table->integer('used')->default(0);
            $table->dateTime('created_dt')->nullable();
            $table->string('status', 15)->default('active');

            $table->foreign('tenant_id')->references('tenant_id')->on('tenants');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
