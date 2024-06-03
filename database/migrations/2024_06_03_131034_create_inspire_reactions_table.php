<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inspire_reactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inspire_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('reaction_type'); // Define reaction type as an integer
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('inspire_id')->references('id')->on('inspire')->onDelete('cascade');
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
        Schema::dropIfExists('inspire_reactions');
    }
};
