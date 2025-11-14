<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->string('user_id', 50)->primary();
            $table->string('name', 255)->nullable();
            $table->string('email', 255)->unique();
            $table->string('password', 255)->nullable();
            $table->string('photo', 255)->nullable();
            $table->string('major', 255)->nullable();
            $table->integer('year_generation')->nullable();
            $table->string('job', 255)->nullable();
            $table->string('location', 255)->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->string('status', 15)->nullable()->default('inactive');
            $table->string('lat', 20)->nullable();
            $table->string('lng', 20)->nullable();
            $table->string('role', 50)->nullable();
            $table->string('otp_code', 6)->nullable();
            $table->string('marital_status', 20)->nullable();
            $table->integer('dependents')->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->timestamp('created_dt')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
