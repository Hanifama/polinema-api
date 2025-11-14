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
        Schema::create('discussion_like', function (Blueprint $table) {
            $table->string('user_id', 50);
            $table->string('discus_id', 50);
            $table->dateTime('created_dt');
        
            $table->primary(['user_id', 'discus_id', 'created_dt']);
        
            $table->foreign('user_id')->references('user_id')->on('users');
            $table->foreign('discus_id')->references('discus_id')->on('discussion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discussion_like');
    }
};
