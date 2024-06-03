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
            // Rename column video_url to media_url
            $table->renameColumn('video_url', 'media_url');
            
            // Add new column type before title column
            $table->enum('type', ['video', 'image'])->after('id');
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
            // Rename column media_url back to video_url
            $table->renameColumn('media_url', 'video_url');
            
            // Drop the type column
            $table->dropColumn('type');
        });
    }
};
