<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Opinion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opinions', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->unsignedBigInteger('relation_id');
            $table->unsignedBigInteger('user_id')->comment('user who made the opinion');
            $table->unsignedBigInteger('type')->comment('0 - I have no opinion, 1 - Statement is False, 2 - Statement is true but has no impact on the context, 3 - Statement is true and has impact on the context');
            $table->double('confidence', 8, 4)->comment('If the opinion is changed to 0');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('opinions');
    }
}
