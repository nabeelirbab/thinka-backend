<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UploadTicket extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('upload_tickets', function(Blueprint $table){
            $table->increments('id');
            $table->char('ip_address', 20)->comment('The ip address of the requesting user. This would prevent brute force guessing of ticket ids');
            $table->integer('expected_file_quantity')->comment('Number of files expected.');
            $table->text('note')->comment('What is the reason for the upload');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('upload_tickets');
    }
}
