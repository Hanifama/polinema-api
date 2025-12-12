<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users_blocked', function (Blueprint $table) {
            $table->string('blocked_id')->primary();
            $table->string('blocker_user_id'); // user yang memblokir
            $table->string('blocked_user_id'); // user yang diblokir
            $table->string('reason')->nullable();
            $table->boolean('is_active')->default(true);

            // custom timestamp
            $table->timestamp('created_dt')->nullable();

            // foreign key
            $table->foreign('blocker_user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('blocked_user_id')->references('user_id')->on('users')->onDelete('cascade');

            $table->unique(['blocker_user_id', 'blocked_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users_blocked');
    }
};
