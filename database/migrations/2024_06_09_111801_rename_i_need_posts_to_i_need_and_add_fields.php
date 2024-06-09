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
        // Rename the table
        Schema::rename('i_need_posts', 'i_need');

        // Add new fields to the renamed table
        Schema::table('i_need', function (Blueprint $table) {
            $table->string('post_location')->nullable()->after('post_status');
            $table->string('post_experience')->nullable()->after('post_location');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove new fields
        Schema::table('i_need', function (Blueprint $table) {
            $table->dropColumn('post_location');
            $table->dropColumn('post_experience');
        });

        // Rename the table back to original
        Schema::rename('i_need', 'i_need_posts');
    }
};
