<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DefaultRelationType extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table = 'relation_types';
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table($table)->truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $entries = [
            ['id' => 1, 'name' => 'But', 'description' => 'But. The standard counter statement.', 'symbol' => '-', 'default_impact' => -1, 'relevance' => 10, 'relevance_window' => -1],
            ['id' => 2, 'name' => 'Because', 'description' => 'Because. The standard supporting statement.', 'symbol' => '+', 'default_impact' => 1, 'relevance' => 0, 'relevance_window' => -1],
            ['id' => 3, 'name' => 'Adjustment', 'description' => 'Adjustment. A slightly alternative statement.', 'symbol' => '<>', 'default_impact' => 0, 'relevance' => 100, 'relevance_window' => -1],
            ['id' => 4, 'name' => 'And', 'description' => 'And. A logical group of statements.', 'symbol' => '&', 'default_impact' => 0, 'relevance' => 90, 'relevance_window' => -1],
            ['id' => 5, 'name' => 'Definition', 'description' => 'Definition. A expanded description.', 'symbol' => '*', 'default_impact' => 1, 'relevance' => 40, 'relevance_window' => -1],
            ['id' => 6, 'name' => 'Synonym', 'description' => 'Synonym. A synonymous statement.', 'symbol' => '=', 'default_impact' => 0, 'relevance' => 70, 'relevance_window' => -1],
            ['id' => 7, 'name' => 'Opposite', 'description' => 'Opposite. An opposite statement.', 'symbol' => '%', 'default_impact' => 0, 'relevance' => 80, 'relevance_window' => -1],
            ['id' => 8, 'name' => 'Excluding', 'description' => 'Excluding. A single example to exclude.', 'symbol' => '-', 'default_impact' => -1, 'relevance' => 13, 'relevance_window' => -1],
            ['id' => 9, 'name' => 'Including', 'description' => 'Including. A single example to include.', 'symbol' => '+', 'default_impact' => 1, 'relevance' => 5, 'relevance_window' => -1],
            ['id' => 10, 'name' => 'Like', 'description' => 'Like. A general supporting example.', 'symbol' => 'e.g', 'default_impact' => 1, 'relevance' => 6, 'relevance_window' => -1],
            ['id' => 11, 'name' => 'Note', 'description' => 'Note. A note of no critical impact.', 'symbol' => '*', 'default_impact' => 0, 'relevance' => 30, 'relevance_window' => -1],
            ['id' => 12, 'name' => 'However', 'description' => 'However. A general counter example.', 'symbol' => '-', 'default_impact' => -1, 'relevance' => 15, 'relevance_window' => -1],
            ['id' => 13, 'name' => 'Reference.', 'description' => 'Reference. A source link or index title.', 'symbol' => '*', 'default_impact' => 1, 'relevance' => 50, 'relevance_window' => -1],
            ['id' => 14, 'name' => 'If', 'description' => 'If. The logical premise for a consequence.', 'symbol' => 'IF', 'default_impact' => 0, 'relevance' => 22, 'relevance_window' => -1],
            ['id' => 15, 'name' => 'Then', 'description' => 'Then. The logical consequence of a premise.', 'symbol' => 'THEN', 'default_impact' => 1, 'relevance' => 25, 'relevance_window' => -1],
        ];
        \DB:: table($table) -> insert($entries);
    }
}
