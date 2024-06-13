<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('i_need_skills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('i_need_id');
            $table->unsignedBigInteger('skill_id');
            $table->timestamps();

            // Foreign keys
            $table->foreign('i_need_id')->references('id')->on('i_need')->onDelete('cascade');
            $table->foreign('skill_id')->references('id')->on('skills')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('i_need_skills');
    }
};
