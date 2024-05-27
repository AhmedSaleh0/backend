<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('skills', function (Blueprint $table) {
            // Change category and subsub_category to integer fields
            $table->unsignedBigInteger('category')->change();
            $table->unsignedBigInteger('sub_category')->change();

            // Add foreign key constraints
            $table->foreign('category')->references('id')->on('skills_categories')->onDelete('cascade');
            $table->foreign('sub_category')->references('id')->on('skills_sub_categories')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('skills', function (Blueprint $table) {
            // Drop foreign key constraints
            $table->dropForeign(['category']);
            $table->dropForeign(['sub_category']);

            // Revert category and subsub_category to varchar
            $table->string('category')->change();
            $table->string('sub_category')->change();
        });
    }
};
