<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserExperiencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_experiences', function (Blueprint $table) {
            $table->string('experience_id')->primary();
            $table->string('user_id');
            $table->string('company_name');
            $table->string('company_category');
            $table->string('position');
            $table->integer('start_year');
            $table->integer('end_year')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('created_dt')->nullable();
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_experiences');
    }
}
