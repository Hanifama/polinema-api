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
        Schema::create('favorite_friends', function (Blueprint $table) {
            $table->string('favorite_id')->primary();
            $table->string('user_id');
            $table->string('friend_user_id'); 
            $table->timestamp('created_dt')->useCurrent();
        
            // Foreign keys
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('friend_user_id')->references('user_id')->on('users')->onDelete('cascade');
        
            // Supaya tidak dobel favorit
            $table->unique(['user_id', 'friend_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorite_friends');
    }
};
