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
        Schema::create('chat_message', function (Blueprint $table) {
            $table->string('user_id', 50);
            $table->string('chroom_id', 50);
            $table->text('message')->nullable();
            $table->string('attachment', 255)->nullable();
            $table->string('attachment_mime', 50)->nullable();
            $table->dateTime('created_dt');
            $table->boolean('is_deliver')->default(0);
            $table->boolean('is_read')->default(0);

            $table->primary(['user_id', 'chroom_id', 'created_dt']);

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('chroom_id')->references('chroom_id')->on('chat_room')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_message');
    }
};
