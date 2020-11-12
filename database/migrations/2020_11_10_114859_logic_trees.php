<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LogicTrees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logic_trees', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('statement_id');
            $table->text('name');
            $table->boolean('is_public');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('logic_trees', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('statement_id')->references('id')->on('statements');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logic_trees');
    }
}
