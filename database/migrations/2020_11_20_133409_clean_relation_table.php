<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CleanRelationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('relations', function(Blueprint $table){
            $table->unsignedBigInteger('parent_relation_id')->nullable()->change();
            $table->dropColumn('statement_id_1');
            $table->renameColumn('statement_id_2', 'statement_id');
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
