<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppVersionsTable extends Migration
{
    public function up(): void
    {
        Schema::create('app_versions', function (Blueprint $table) {
            $table->id();
            $table->string('version_code');
            $table->string('platform');
            $table->string('version_name')->nullable();
            $table->text('version_description');
            $table->boolean('is_latest')->default(false);
            $table->boolean('is_allowed')->default(true);
            $table->dateTime('released_date');
            $table->string('created_by');
            $table->string('updated_by');
            $table->timestamps();

            $table->unique(['version_code', 'platform']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_versions');
    }
}
