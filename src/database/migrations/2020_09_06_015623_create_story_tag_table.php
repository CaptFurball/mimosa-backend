<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoryTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('story_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('story_id')
                ->references('id')
                ->on('stories')
                ->onDelete('cascade');
                
            $table->foreignId('tag_id')
                ->references('id')
                ->on('tags')
                ->onDelete('cascade');

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
        Schema::dropIfExists('story_tag');
    }
}
