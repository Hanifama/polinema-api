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
        Schema::create('discussion_comment', function (Blueprint $table) {
            $table->string('comment_id')->primary(); 
            $table->string('user_id', 50);
            $table->string('discus_id', 50);
            $table->dateTime('created_dt');
            $table->text('content');
            $table->string('attachment', 255)->nullable();
            $table->string('attachment_mime', 50)->nullable();
        
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('discus_id')->references('discus_id')->on('discussion')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discussion_comment');
    }
};
