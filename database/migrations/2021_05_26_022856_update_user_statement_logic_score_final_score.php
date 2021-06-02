<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUserStatementLogicScoreFinalScore extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement("DROP VIEW IF EXISTS user_statement_logic_scores");
        \DB::statement("
            CREATE VIEW user_statement_logic_scores AS
            SELECT 
                user_id,
                statement_id, 
                opinion_count, 
                flag, 
                summed_opinion_score_truth, 
                max_opinion_score_truth, 
                min_opinion_score_truth, 
                max_opinion_confidence,
                IF(flag = 0,
                    max_opinion_confidence,
                    IF(
                        flag = 1,
                        IF(summed_opinion_score_truth > 1, 1, summed_opinion_score_truth),
                        IF(
                            flag = 2,
                            IF(summed_opinion_score_truth < -1, -1, summed_opinion_score_truth),
                            0
                        )
                    )
                ) AS final_score
            FROM (
                SELECT 
                    opinions.user_id AS user_id,
                    relations.statement_id AS statement_id,
                    count(opinions.id) AS opinion_count,
                    MIN(opinions.confidence) AS min_opinion_confidence,
                    MAX(opinions.confidence) AS max_opinion_confidence,
                    MAX(opinion_calculated_columns.score_truth) AS max_opinion_score_truth,
                    MIN(opinion_calculated_columns.score_truth) AS min_opinion_score_truth,
                    IF(SUM(opinion_calculated_columns.score_truth) > 1, 1, SUM(opinion_calculated_columns.score_truth)) AS summed_opinion_score_truth,
                    IF(
                        MIN(opinion_calculated_columns.score_truth) = 0 && MAX(opinion_calculated_columns.score_truth) = 0,
                        0,
                        IF(
                            MIN(opinion_calculated_columns.score_truth) >= 0 && MAX(opinion_calculated_columns.score_truth) > 0, 
                            1, 
                            IF(MIN(opinion_calculated_columns.score_truth) < 0 && MAX(opinion_calculated_columns.score_truth) <= 0, 2, 3)
                        )
                    ) AS flag
                FROM opinions
                LEFT JOIN relations on relations.id = opinions.relation_id
                LEFT JOIN opinion_calculated_columns on opinion_calculated_columns.id = opinions.id
                WHERE 
                    relations.published_at IS NOT NULL
                    AND relations.deleted_at IS NULL
                    AND opinions.deleted_at IS NULL
                GROUP BY opinions.user_id, relations.statement_id
                
            ) AS core_opinions
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
