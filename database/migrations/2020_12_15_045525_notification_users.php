<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NotificationUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_users', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->unsignedBigInteger('notification_id')->comment('user to notify');
            $table->unsignedBigInteger('user_id')->comment('user to notify');
            $table->tinyInteger('status')->default(0)->comment('0 - unopened, 1 - read but not opened, 2 - opened');
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
        Schema::dropIfExists('notifications');
    }
}
