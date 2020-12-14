<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeIsPublicPublishedAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('logic_trees', function(Blueprint $table){
            $table->dropColumn('is_public');
            $table->timestamp('published_at')->nullable()->after('name');
        });
        Schema::table('statements', function(Blueprint $table){
            $table->dropColumn('is_public');
            $table->timestamp('published_at')->nullable()->after('synopsis');
        });
        Schema::table('relations', function(Blueprint $table){
            $table->dropColumn('is_public');
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
