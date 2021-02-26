<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Playbyplay extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('play_by_play', function (Blueprint $table) {
            $table->id();
            $table->integer('gid');
            $table->integer('uid');
            $table->text('action');
            $table->mediumText('data');
            $table->timestamps();
            $table->integer('game_no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('play_by_play');
    }
}
