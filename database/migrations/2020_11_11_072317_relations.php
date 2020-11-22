<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Relations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('relations', function(Blueprint $table){
            $table->unsignedBigInteger('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('relation_type_id');
            $table->unsignedBigInteger('logic_tree_id');
            $table->unsignedBigInteger('statement_id_1'); // parent
            $table->unsignedBigInteger('statement_id_2')->nullable(); // child
            $table->float('impact')->nullable();
            $table->float('impact_amount')->nullable();
            $table->float('risk_plan_cost')->nullable();
            $table->float('residual_risk')->nullable();
            $table->float('relevance_row')->nullable();
            $table->boolean('is_public');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('relations', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('relation_type_id')->references('id')->on('relation_types');
            $table->foreign('logic_tree_id')->references('id')->on('logic_trees');
            $table->foreign('statement_id_1')->references('id')->on('statements');
            $table->foreign('statement_id_2')->references('id')->on('statements');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('relations');
        
    }
}
