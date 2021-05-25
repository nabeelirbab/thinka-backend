<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveOldRelationColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('relations', function(Blueprint $table){
            if(Schema::hasColumn('relations', 'impact')){
                $table->dropColumn('impact');
            }
            if(Schema::hasColumn('relations', 'risk_plan_cost')){
                $table->dropColumn('risk_plan_cost');
            }
            if(Schema::hasColumn('relations', 'residual_risk')){
                $table->dropColumn('residual_risk');
            }
            if(Schema::hasColumn('relations', 'residual_impact')){
                $table->dropColumn('residual_impact');
            }
            if(Schema::hasColumn('relations', 'impact_amount')){
                $table->dropColumn('impact_amount');
            }
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
