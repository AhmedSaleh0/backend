<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('i_need_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ineed_id')->constrained('i_need')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('reaction_type');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('i_need_reactions');
    }
};
