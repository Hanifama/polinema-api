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
        Schema::create('posts', function (Blueprint $table) {
            $table->string('post_id', 50)->primary();
            $table->string('wall_owner_id', 50);
            $table->string('author_id', 50);
            $table->text('content')->nullable();
            $table->string('attachment', 255)->nullable();
            $table->string('type', 20)->default('text');
            $table->string('privacy', 20)->default('public');
            $table->dateTime('created_dt')->nullable();
        
            $table->foreign('wall_owner_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('author_id')->references('user_id')->on('users')->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
