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
        Schema::create('user_reports', function (Blueprint $table) {
            $table->uuid('report_id')->primary();

            $table->string('reported_user_id', 50); // user yang melapor
            $table->string('reporter_user_id', 50); // user yang dilapor

            $table->string('category_code', 100);
            $table->text('description')->nullable();

            $table->enum('status', ['pending', 'reviewing', 'resolved', 'rejected'])
                ->default('pending');

            $table->text('admin_note')->nullable();
            $table->string('resolved_by', 50)->nullable();

            $table->timestamp('created_dt')->nullable();
            $table->timestamp('updated_dt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_reports');
    }
};
