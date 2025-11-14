<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('tenant_categories', function (Blueprint $table) {
            $table->string('tencat_id', 50)->primary();
            $table->string('name', 255)->nullable();
            $table->string('icon', 255)->nullable();
            $table->string('status', 15)->default('active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_categories');
    }
};
