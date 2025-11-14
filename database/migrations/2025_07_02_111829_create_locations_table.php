<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['provinsi', 'kota', 'kabupaten', 'kecamatan', 'kelurahan', 'desa']);
            $table->unsignedBigInteger('parent_id')->nullable(); 
            $table->string('lat', 50)->nullable();
            $table->string('lng', 50)->nullable();
            $table->integer('order')->default(0);

            $table->foreign('parent_id')->references('id')->on('locations')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
