<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NotificationStatementUpdates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_relation_updates', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->unsignedBigInteger('notification_id');
            $table->unsignedBigInteger('user_id')->comment('user who made the changes');
            $table->unsignedBigInteger('relation_id');
            $table->text('message');
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
        Schema::dropIfExists('notification_relation_updates');
    }
}
