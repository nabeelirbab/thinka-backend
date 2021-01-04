<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NotificationSubRelationUpdateUserRelations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_sub_relation_update_user_relations', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->unsignedBigInteger('notification_sub_relation_update_id');
            $table->unsignedBigInteger('user_id')->comment('The user to notify');
            $table->unsignedBigInteger('relation_id')->comment('The parent relation');
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
        // Schema::dropIfExists('notification_sub_relation_update_user_relations');
    }
}
