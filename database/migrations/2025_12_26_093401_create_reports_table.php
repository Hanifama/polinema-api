<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_reports', function (Blueprint $table) {
            $table->string('report_id')->primary();

            // tipe konten yang dilaporkan
            $table->enum('report_type', [
                'discussion',
                'post_wall'
            ]);

            // target report (salah satu saja yang terisi)
            $table->string('reported_discus_id', 50)->nullable();
            $table->string('reported_post_id', 50)->nullable();

            // user yang melapor
            $table->string('reporter_user_id', 50);

            // kategori laporan
            $table->string('category_code', 100);

            $table->text('description')->nullable();

            $table->enum('status', [
                'pending',
                'reviewing',
                'resolved',
                'rejected'
            ])->default('pending');

            // catatan admin
            $table->text('admin_note')->nullable();
            $table->string('resolved_by', 50)->nullable();

            $table->timestamp('created_dt')->nullable();
            $table->timestamp('updated_dt')->nullable();

            // index
            $table->index('report_type');
            $table->index('reporter_user_id');
            $table->index('reported_discus_id');
            $table->index('reported_post_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_reports');
    }
};
