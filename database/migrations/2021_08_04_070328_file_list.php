<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FileList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_lists', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('upload_ticket_id');
            $table->text('name');
            $table->text('original_name');
            $table->char('extension', 10);
            $table->double('size', 20, 3)->comment('size in kb');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('file_lists', function (Blueprint $table) {
            $table->foreign('upload_ticket_id')->references('id')->on('upload_tickets');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('file_lists');
    }
}
