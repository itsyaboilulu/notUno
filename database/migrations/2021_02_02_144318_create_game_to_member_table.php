<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameToMemberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_to_member', function (Blueprint $table) {
            $table->integer('gid');
            $table->integer('uid');
            $table->mediumText('hand');
            $table->boolean('admin')->default(0);;
            $table->primary(array('gid', 'uid'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_to_member');
    }
}
