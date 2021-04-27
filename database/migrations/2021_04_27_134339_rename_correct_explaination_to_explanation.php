<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameCorrectExplainationToExplanation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('statement_types', function (Blueprint $table) {
            $table->renameColumn('explaination', 'explanation');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('explaination', function (Blueprint $table) {
            $table->renameColumn('explanation', 'explaination');
        });
    }
}
