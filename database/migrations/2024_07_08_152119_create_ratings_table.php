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
            $table->unsignedBigInteger('rateable_id'); // the entity being rated
            $table->string('rateable_type'); // type of the entity being rated (iNeed, iCan)
            $table->integer('rating')->default(0);
            $table->text('review')->nullable();
            $table->string('status')->default('Pending'); // status of the review
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ratings');
    }
};
