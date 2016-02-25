<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('Hidden')->default(0);
            $table->integer('CourseID');
            $table->integer('ThumbnailID');
            $table->string('Title');
            $table->integer('NoOfFreeQuestions')->default(5);
            $table->string('Photo')->default(null);
            $table->string('Video')->default(null);
            $table->string('Description')->default(null);
            $table->integer('visited')->default(0);
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
        Schema::drop('posts');
    }
}
