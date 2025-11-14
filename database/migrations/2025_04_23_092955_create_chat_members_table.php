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
        Schema::create('chat_member', function (Blueprint $table) {
            $table->string('user_id', 50);
            $table->string('chroom_id', 50);
            $table->dateTime('joined_dt')->nullable();
            $table->string('status', 15)->nullable();

            $table->primary(['user_id', 'chroom_id']);

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('chroom_id')->references('chroom_id')->on('chat_room')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_member');
    }
};
