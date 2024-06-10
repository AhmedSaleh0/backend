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
            $table->enum('experience', ['Entry', 'Intermediate', 'Expert'])->change();
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
            $table->string('experience')->change();
        });
    }
};
