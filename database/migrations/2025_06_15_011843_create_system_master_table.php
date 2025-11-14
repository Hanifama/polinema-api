<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('system_master', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('sub_category')->nullable();
            $table->string('key');
            $table->string('value');
            $table->text('description')->nullable();
            $table->string('status')->default('active');
            $table->timestamp('created_dt')->useCurrent();
            $table->string('created_by')->default('system');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_master');
    }
};
