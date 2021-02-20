<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('password');
            $table->string('current_card')->nullable(true);
            $table->mediumText('deck')->nullable(true);
            $table->integer('turn')->nullable(true);
            $table->mediumText('order')->nullable(true);
            $table->mediumText('setting')->nullable(true);
            $table->boolean('started')->default(0);
            $table->mediumText('game_data')->nullable(true);
            $table->timestamp('updated');
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
        Schema::dropIfExists('game');
    }
}
