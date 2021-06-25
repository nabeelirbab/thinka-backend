<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateNotificationSubRelationUpdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notification_sub_relation_updates', function(Blueprint $table){
            $table->unsignedTinyInteger('type')->default(1)->comment('1 - general, 2 - publish co author, 3 - publish bookmarker');
        });
        Schema::table('notification_sub_relation_update_user_relations', function(Blueprint $table){
            $table->unsignedTinyInteger('subscriber_type')->comment('1 - general, 2 - publish')->after('user_id');
            $table->dropColumn(['relation_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
