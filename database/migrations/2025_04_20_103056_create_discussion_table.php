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
        Schema::create('discussion', function (Blueprint $table) {
            $table->string('discus_id', 50)->primary();
            $table->string('title', 255);
            $table->text('content');
            $table->string('attachment', 255)->nullable();
            $table->string('attachment_mime', 50)->nullable();
            $table->dateTime('created_dt');
            $table->dateTime('approved_dt')->nullable();
            $table->string('user_id', 50);
            $table->integer('view_cnt')->default(0);
            $table->integer('like_cnt')->default(0);
            $table->integer('comment_cnt')->default(0);

            $table->foreign('user_id')->references('user_id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discussion');
    }
};
