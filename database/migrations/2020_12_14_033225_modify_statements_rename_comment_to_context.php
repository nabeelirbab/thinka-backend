<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyStatementsRenameCommentToContext extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('statements', function (Blueprint $table) {
            $table->text('context')->nullable()->after('statement_certainty')->comment('A tag label representing the context of this statement.');
        });
        if (Schema::hasColumn('statements', 'comment')){
            Schema::table('statements', function (Blueprint $table){
                $table->dropColumn('comment');
            });
        }
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
