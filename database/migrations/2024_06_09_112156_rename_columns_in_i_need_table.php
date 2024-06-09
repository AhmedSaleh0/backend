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
        Schema::table('i_need', function (Blueprint $table) {
            $table->renameColumn('post_title', 'title');
            $table->renameColumn('post_short_description', 'short_description');
            $table->renameColumn('post_image', 'image');
            $table->renameColumn('post_price', 'price');
            $table->renameColumn('post_price_type', 'price_type');
            $table->renameColumn('post_status', 'status');
            $table->renameColumn('post_location', 'location');
            $table->renameColumn('post_experience', 'experience');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('i_need', function (Blueprint $table) {
            $table->renameColumn('title', 'post_title');
            $table->renameColumn('short_description', 'post_short_description');
            $table->renameColumn('image', 'post_image');
            $table->renameColumn('price', 'post_price');
            $table->renameColumn('price_type', 'post_price_type');
            $table->renameColumn('status', 'post_status');
            $table->renameColumn('location', 'post_location');
            $table->renameColumn('experience', 'post_experience');
        });
    }
};
