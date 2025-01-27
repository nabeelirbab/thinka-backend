<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('relations', function (Blueprint $table) {
            // Add foreign key for user_id referencing users.id
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade'); // Optional: Define onDelete behavior

            // Add foreign key for relation_type_id referencing relation_types.id
            $table->foreign('relation_type_id')
                ->references('id')
                ->on('relation_types')
                ->onDelete('cascade'); // Optional: Define onDelete behavior

            // Add foreign key for logic_tree_id referencing logic_trees.id
            $table->foreign('logic_tree_id')
                ->references('id')
                ->on('logic_trees')
                ->onDelete('cascade'); // Optional: Define onDelete behavior

            // Add foreign key for statement_id_1 referencing statements.id
            $table->foreign('statement_id_1')
                ->references('id')
                ->on('statements')
                ->onDelete('cascade'); // Optional: Define onDelete behavior

            // Add foreign key for statement_id_2 referencing statements.id
            $table->foreign('statement_id_2')
                ->references('id')
                ->on('statements')
                ->onDelete('cascade'); // Optional: Define onDelete behavior
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('relations', function (Blueprint $table) {
            // Drop the foreign keys
            $table->dropForeign(['user_id']);
            $table->dropForeign(['relation_type_id']);
            $table->dropForeign(['logic_tree_id']);
            $table->dropForeign(['statement_id_1']);
            $table->dropForeign(['statement_id_2']);
        });
    }
}
