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
        Schema::create('i_can_reactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ican_id');
            $table->unsignedBigInteger('user_id');
            $table->string('reaction_type');
            $table->timestamps();

            $table->foreign('ican_id')->references('id')->on('i_can')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('i_can_reactions');
    }
};
