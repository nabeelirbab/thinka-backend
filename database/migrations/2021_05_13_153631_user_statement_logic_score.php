<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserStatementLogicScore extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * blue - 1 - all positive
         * white - 0 - all zero
         * black - 2 - all negative
         * red - 2 - contradicting
         */
        \DB::statement("DROP VIEW IF EXISTS user_statement_logic_scores");
        \DB::statement("
            CREATE VIEW user_statement_logic_scores AS
            SELECT 
                opinions.user_id,
                relations.statement_id,
                count(opinions.id) as opinion_count,
                IF(
                    MIN(opinion_calculated_columns.score_truth) = 0 && MAX(opinion_calculated_columns.score_truth) = 0,
                    0, 
                    IF(
                        MIN(opinion_calculated_columns.score_truth) >= 0 && MAX(opinion_calculated_columns.score_truth) > 0, 
                        1, 
                        IF(MIN(opinion_calculated_columns.score_truth) < 0 && MAX(opinion_calculated_columns.score_truth) <= 0, 2, 3)
                    )
                ) as flag
            FROM opinions
            LEFT JOIN relations on relations.id = opinions.relation_id
            LEFT JOIN opinion_calculated_columns on opinion_calculated_columns.id = opinions.id
            GROUP BY opinions.user_id, relations.statement_id
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement("DROP VIEW IF EXISTS user_statement_logic_scores");
    }
}
