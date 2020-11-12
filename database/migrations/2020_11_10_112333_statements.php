<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Statements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statements', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('statement_type_id');
            $table->unsignedInteger('scope_id')->nullable();
            $table->unsignedInteger('statement_certainty_id')->nullable();
            $table->text('text'); // original name is StatementText
            $table->text('synopsis')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('is_public');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('statements', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('statement_type_id')->references('id')->on('statement_types');
            $table->foreign('statement_certainty_id')->references('id')->on('statement_certainties');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('statements');
    }
}
