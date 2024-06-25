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
        Schema::table('i_can', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->default(null)->change();
        });

        Schema::table('i_need', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('i_can', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->default(14)->change();
        });

        Schema::table('i_need', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->default(14)->change();
        });
    }
};
