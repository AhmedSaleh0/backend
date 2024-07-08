<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // who gives the rating
            $table->unsignedBigInteger('rated_id'); // who/what is being rated
            $table->string('type'); // 'iNeed' or 'iCan'
            $table->integer('rating')->default(0);
            $table->text('review')->nullable();
            $table->string('status')->default('Pending'); // status of the review
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('rated_id')->references('id')->on('users')->onDelete('cascade'); // Assuming the rated entity is a user
        });
    }

    public function down()
    {
        Schema::dropIfExists('ratings');
    }
};
