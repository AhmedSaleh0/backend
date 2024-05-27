<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('skills_sub_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('skills_categories')->onDelete('cascade');
            $table->string('name')->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('skills_sub_categories');
    }
};
