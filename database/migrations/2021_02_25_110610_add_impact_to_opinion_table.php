<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImpactToOpinionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('opinions', function (Blueprint $table) {
            $table->float('residual_risk')->nullable()->after('confidence');
            $table->float('risk_plan_cost')->nullable()->after('confidence');
            $table->float('impact_amount')->nullable()->after('confidence');
            $table->float('impact')->nullable()->after('confidence');
            $table->unsignedBigInteger('type')->nullable()->change();
        });
        DB::statement('ALTER TABLE `opinions` MODIFY `confidence` DOUBLE(8,4) NULL;');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('opinion', function (Blueprint $table) {
            //
        });
    }
}
