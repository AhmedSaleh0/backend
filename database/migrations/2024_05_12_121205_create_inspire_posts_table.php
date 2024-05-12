<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInspirePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inspire_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('video_url')->nullable();  // Assuming the video URL is optional
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('active');  // Example statuses: active, inactive
            $table->unsignedBigInteger('views')->default(0);
            $table->string('category');
            $table->string('sub_category');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inspire_posts');
    }
}