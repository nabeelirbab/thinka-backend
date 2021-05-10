<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOpinionScoreColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement("DROP VIEW IF EXISTS opinion_calculated_columns");
        \DB::statement("
            CREATE VIEW opinion_calculated_columns AS
            SELECT 
                opinions.id as id,
                IF(
                    opinions.type = 0,
                    0,
                    IF(
                        opinions.type = 1,
                        0,
                        IF(
                            opinions.type = 2,
                            0,
                            IF(
                                opinions.type = 3,
                                opinions.confidence * relations.impact_amount,
                                0
                            )
                        )
                    )
                ) AS score_relation,
                IF(
                    opinions.type = 0,
                    0,
                    IF(
                        opinions.type = 1,
                        0,
                        IF(
                            opinions.type = 2,
                            opinions.confidence,
                            IF(
                                opinions.type = 3,
                                opinions.confidence,
                                0
                            )
                        )
                    )
                ) AS score_statement,
                opinions.confidence * IF( opinions.type = 1, -1, 1 ) AS score_truth,
                opinions.confidence * IF( opinions.type = 3, 1, 0 ) AS score_truth_parent
            FROM opinions
            LEFT JOIN relations on relations.id = opinions.relation_id
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement("DROP VIEW IF EXISTS opinion_calculated_columns");
    }
}
