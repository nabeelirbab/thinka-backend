<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class StatementTrendingProcedure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * Replace the following keywords]
         * Statement statements
         * 
         */
        $procedureName = "StatementTrending";
        $procedure =  "
        CREATE PROCEDURE `$procedureName`()
            NO SQL
        SELECT 
        -- Returns Statements that are trending.
        -- ---------------------------
        
        S.*
        
        FROM statements S 
        
        ORDER BY RAND() LIMIT 10
        ";
        \DB::unprepared("DROP procedure IF EXISTS $procedureName");
        \DB::unprepared($procedure);
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
