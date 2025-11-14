<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('friend', function (Blueprint $table) {
            $table->string('user_id_1', 50);
            $table->string('user_id_2', 50);
            $table->dateTime('created_dt')->nullable();
            $table->dateTime('accepted_dt')->nullable();
            $table->string('status', 15)->default('pending');
        
            $table->primary(['user_id_1', 'user_id_2']);
        
            $table->foreign('user_id_1')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('user_id_2')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('friend');
    }
};
