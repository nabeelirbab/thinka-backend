<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RelationContextLock extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_relation_context_locks', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('relation_id');
            $table->unsignedBigInteger('root_relation_id')->nullable();
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
        Schema::dropIfExists('user_relation_context_locks');
    }
}
