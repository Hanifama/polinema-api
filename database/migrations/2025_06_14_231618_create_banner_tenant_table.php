<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBannerTenantTable extends Migration
{
    public function up(): void
    {
        Schema::create('banner_tenant', function (Blueprint $table) {
            $table->string('banner_id')->primary();
            $table->string('image');
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('inactive');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banner_tenant');
    }
}
