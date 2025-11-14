<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->string('tenant_id', 50)->primary();
            $table->string('tencat_id', 50);
            $table->string('name', 255)->nullable();
            $table->string('banner', 255)->nullable();
            $table->text('address')->nullable();
            $table->text('about')->nullable();
            $table->string('image_1', 255)->nullable();
            $table->string('image_2', 255)->nullable();
            $table->string('image_3', 255)->nullable();
            $table->string('image_4', 255)->nullable();
            $table->string('lat', 20)->nullable();
            $table->string('lng', 20)->nullable();
            $table->string('status', 15)->default('active');
            $table->dateTime('created_dt')->nullable();
            $table->string('created_by', 50)->nullable();

            $table->foreign('tencat_id')->references('tencat_id')->on('tenant_categories');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
