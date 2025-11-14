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
        Schema::create('community_categories', function (Blueprint $table) {
            $table->string('category_id')->primary(); 
            $table->string('name');
            $table->string('photo')->nullable();
            $table->timestamp('created_dt')->nullable();
        });

        
        Schema::table('communities', function (Blueprint $table) {
            $table->string('category_id')->nullable()->after('logo'); 

            $table->foreign('category_id')
                ->references('category_id')
                ->on('community_categories')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('communities', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });

        Schema::dropIfExists('community_categories');
    }
};
