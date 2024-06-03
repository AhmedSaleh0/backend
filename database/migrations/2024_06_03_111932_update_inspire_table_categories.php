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
        Schema::table('inspire', function (Blueprint $table) {
            // Drop the current string columns
            $table->dropColumn(['category', 'sub_category']);
        });

        Schema::table('inspire', function (Blueprint $table) {
            // Add new integer columns
            $table->unsignedBigInteger('category')->after('media_url');
            $table->unsignedBigInteger('sub_category')->after('category');

            // Add foreign key constraints
            $table->foreign('category')->references('id')->on('skills_categories')->onDelete('cascade');
            $table->foreign('sub_category')->references('id')->on('skills_sub_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inspire', function (Blueprint $table) {
            // Drop the foreign key constraints
            $table->dropForeign(['category']);
            $table->dropForeign(['sub_category']);

            // Drop the integer columns
            $table->dropColumn(['category', 'sub_category']);
        });

        Schema::table('inspire', function (Blueprint $table) {
            // Add the original string columns back
            $table->string('category')->after('media_url');
            $table->string('sub_category')->after('category');
        });
    }
};
