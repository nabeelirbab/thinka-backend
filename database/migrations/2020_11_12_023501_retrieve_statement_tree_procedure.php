<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RetrieveStatementTreeProcedure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * Replace the following keywords
         * RT.TypeID RT.id
         * TypeID relation_type_id
         * StatementID2 statement_id_2
         * StatementID1 statement_id_1
         * StatementID id
         * StatementText text
         * UserID user_id
         * RelationType relation_types
         * Relation relations
         * Statement statements
         * Public is_public
         * Symbol symbol'
         * Relevance relevance
         * relevanceRow relevance_row
         * Created$$ created_at
         */
        $procedure = "
            CREATE PROCEDURE `statementsrelationsLayers4` (IN `RootClaimID` INT(11), IN `user_id` INT(11), IN `is_public` BOOLEAN)  READS SQL DATA
            SELECT 
            -- Returns all relationss of the Root Claim down 4 layers. 
            -- -----------
            -- RootClaimID is the id of the Root Claim.
            -- user_id is the current user for permissions.
            -- is_public when 1 includes is_public Logic Trees in the results.
            
            
            1 AS 'relationsLayer', RT.symbol, 
            S.text, S.user_id AS AuthorID, 
            R.*, RT.relevance
            
            FROM 
            -- Layer 1
            relations R 
            
            -- Layer 1 outputs
            INNER JOIN relation_types RT ON RT.id = R.relation_type_id 
            INNER JOIN statements S ON S.id = R.statement_id_2
            
            WHERE 
            R.statement_id_1 = @p0 
            AND (R.user_id = @p1 OR (R.is_public AND @p2))
            
            
            UNION
            
            
            SELECT 
            2 AS 'relationsLayer', RT.symbol, 
            S.text, S.user_id, 
            R2.*, RT.relevance
            -- Layer 1
            FROM relations R 
            
            -- Layer 2
            INNER JOIN relations R2 ON R2.statement_id_1 = R.statement_id_2
            
            -- Layer 2 outputs
            INNER JOIN relation_types RT ON RT.id = R2.relation_type_id 
            INNER JOIN statements S ON S.id = R2.statement_id_2
            
            WHERE 
            R.statement_id_1 = @p0 
            AND (R.user_id = @p1 OR (R.is_public AND @p2))
            AND (R2.user_id = @p1 OR (R2.is_public AND @p2))
            
            
            UNION
            
            
            SELECT 
            3 AS 'relationsLayer', RT.symbol, 
            S.text, S.user_id, 
            R3.*, RT.relevance
            -- Layer 1
            FROM relations R 
            
            -- Layer 2
            INNER JOIN relations R2 ON R2.statement_id_1 = R.statement_id_2
            
            -- Layer 3
            INNER JOIN relations R3 ON R3.statement_id_1 = R2.statement_id_2
            
            
            -- Layer 3 outputs
            INNER JOIN relation_types RT ON RT.id = R3.relation_type_id 
            INNER JOIN statements S ON S.id = R3.statement_id_2
            
            WHERE 
            R.statement_id_1 = @p0 
            AND (R.user_id = @p1 OR (R.is_public AND @p2))
            AND (R2.user_id = @p1 OR (R2.is_public AND @p2))
            AND (R3.user_id = @p1 OR (R3.is_public AND @p2))
            
            UNION
            
            SELECT 
            4 AS 'relationsLayer', RT.symbol, 
            S.text, S.user_id, 
            R4.*, RT.relevance
            -- Layer 1
            FROM relations R 
            
            -- Layer 2
            INNER JOIN relations R2 ON R2.statement_id_1 = R.statement_id_2
            
            -- Layer 3
            INNER JOIN relations R3 ON R3.statement_id_1 = R2.statement_id_2
            
            -- Layer 4
            INNER JOIN relations R4 ON R4.statement_id_1 = R3.statement_id_2
            
            -- Layer 4 outputs
            INNER JOIN relation_types RT ON RT.id = R4.relation_type_id 
            INNER JOIN statements S ON S.id = R4.statement_id_2
            
            WHERE 
            R.statement_id_1 = @p0 
            AND (R.user_id = @p1 OR (R.is_public AND @p2))
            AND (R2.user_id = @p1 OR (R2.is_public AND @p2))
            AND (R3.user_id = @p1 OR (R3.is_public AND @p2))
            AND (R4.user_id = @p1 OR (R4.is_public AND @p2))
            
            
            ORDER BY relationsLayer, relevance_row, created_at
        ";
        \DB::unprepared("DROP procedure IF EXISTS statementsrelationsLayers4");
        \DB::unprepared($procedure);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
