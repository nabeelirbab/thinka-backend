<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DefaultStatementCertainty extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table = 'statement_certainties';
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table($table)->truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $entries = [
            ['id' => 1, 'description' => 'Definite', 'degree' => 100],
            ['id' => 2, 'description' => 'Likely', 'degree' => 51],
            ['id' => 3, 'description' => 'Unlikely', 'degree' => 49],
            ['id' => 4, 'description' => 'Never', 'degree' => -100],
            ['id' => 5, 'description' => 'Certain', 'degree' => 100],
            ['id' => 6, 'description' => 'Probable', 'degree' => 51],
            ['id' => 7, 'description' => 'Possible', 'degree' => 1],
            ['id' => 8, 'description' => 'Impossible', 'degree' => -100],
            ['id' => 9, 'description' => 'Neutral', 'degree' => 0],
        ];
        \DB:: table($table) -> insert($entries);
    }
}
