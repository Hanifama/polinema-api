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
        Schema::create('chat_room', function (Blueprint $table) {
            $table->string('chroom_id', 50)->primary();
            $table->string('title', 255)->nullable();
            $table->string('chat_type', 50)->nullable();
            $table->string('image', 255)->nullable();
            $table->dateTime('created_dt')->nullable();
            $table->string('created_by', 50);
            $table->string('status', 15)->nullable();
            $table->integer('member_cnt')->default(0);
            $table->boolean('allow_invite')->default(0);
            $table->boolean('only_admin')->default(0);

            $table->foreign('created_by')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_room');
    }
};
