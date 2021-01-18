<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakePercentatgeColumnsToDouble82 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('alter table relation_types modify default_impact DOUBLE(8,4) DEFAULT 0');
        DB::statement('alter table relation_types modify relevance DOUBLE(8,4) DEFAULT 0');

        DB::statement('alter table relations modify impact DOUBLE(8,4) DEFAULT 0');
        DB::statement('alter table relations modify impact_amount DOUBLE(8,4) DEFAULT 0');
        DB::statement('alter table relations modify risk_plan_cost DOUBLE(8,4) DEFAULT 0');
        DB::statement('alter table relations modify residual_risk DOUBLE(8,4) DEFAULT 0');
        DB::statement('alter table relations modify residual_impact DOUBLE(8,4) DEFAULT 0');
        
        DB::statement('alter table statements modify statement_certainty DOUBLE(8,4) DEFAULT 0');

        DB::statement('alter table statement_certainties modify degree DOUBLE(8,4) DEFAULT 0');
        DB::statement('alter table scopes modify degree DOUBLE(8,4) DEFAULT 0');

        // Schema::table('relation_types', function(Blueprint $table){
        //     $table->double('default_impact', 5, 4)->change();
        //     $table->double('relevance', 5, 4)->change();
        // });
        // Schema::table('relations', function(Blueprint $table){
        //     $table->double('impact', 5, 4)->change();
        //     $table->double('impact_amount', 5, 4)->change();
        //     $table->double('risk_plan_cost', 5, 4)->change();
        //     $table->double('residual_risk', 5, 4)->change();
        //     $table->double('residual_impact', 5, 4)->change();
        // });
        // Schema::table('statements', function(Blueprint $table){
        //     $table->double('statement_certainty', 5, 4)->change();
        // });
        // Schema::table('statement_certainties', function(Blueprint $table){
        //     $table->double('degree', 5, 4)->change();
        // });
        // Schema::table('scopes', function(Blueprint $table){
        //     $table->double('degree', 5, 4)->change();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
