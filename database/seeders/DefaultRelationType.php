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
            ['id' => 1, 'description' => 'However', 'relevance' => 50, 'symbol' => '-'],
            ['id' => 2, 'description' => 'Because', 'relevance' => 0, 'symbol' => '+'],
            ['id' => 3, 'description' => 'Adjustment', 'relevance' => 20, 'symbol' => '<>'],
            ['id' => 4, 'description' => 'And', 'relevance' => 10, 'symbol' => '&'],
            ['id' => 5, 'description' => 'Definition', 'relevance' => 20, 'symbol' => '*'],
            ['id' => 6, 'description' => 'Synonym', 'relevance' => 30, 'symbol' => '='],
            ['id' => 7, 'description' => 'Opposite', 'relevance' => 80, 'symbol' => '%'],
            ['id' => 8, 'description' => 'Except', 'relevance' => 90, 'symbol' => '-1'],
            ['id' => 9, 'description' => 'Including', 'relevance' => 40, 'symbol' => '+1'],
            ['id' => 10, 'description' => 'Logic', 'relevance' => 10, 'symbol' => '*'],
            ['id' => 11, 'description' => 'Facts', 'relevance' => 0, 'symbol' => '+'],
            ['id' => 12, 'description' => 'Or', 'relevance' => 0, 'symbol' => '^'],
            ['id' => 13, 'description' => 'Note', 'relevance' => 0, 'symbol' => '*'],
        ];
        \DB:: table($table) -> insert($entries);
    }
}
