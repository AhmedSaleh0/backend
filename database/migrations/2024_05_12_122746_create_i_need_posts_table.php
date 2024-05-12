<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateINeedPostsTable extends Migration
{
    public function up()
    {
        Schema::create('i_need_posts', function (Blueprint $table) {
            $table->id();
            $table->string('post_title');
            $table->text('post_short_description');
            $table->string('post_image')->nullable(); // Assuming the image is optional
            $table->decimal('post_price', 8, 2); // Adjust precision if needed
            $table->string('post_price_type'); // e.g., 'fixed', 'hourly'
            $table->string('post_status')->default('active'); // e.g., 'active', 'inactive'
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('i_need_posts');
    }
}
