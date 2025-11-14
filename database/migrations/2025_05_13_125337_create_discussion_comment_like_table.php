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
        Schema::create('discussion_comment_like', function (Blueprint $table) {
            $table->string('id')->primary(); 
            $table->string('user_id', 50);
            $table->string('comment_id'); 
            $table->timestamp('created_dt')->useCurrent();
        
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('comment_id')->references('comment_id')->on('discussion_comment')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discussion_comment_like');
    }
};
