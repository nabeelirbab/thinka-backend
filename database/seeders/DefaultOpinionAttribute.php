<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DefaultOpinionAttribute extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table = 'opinion_attributes';
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table($table)->truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $entries = [
            ['id' => 1, 'description' => 'Statement'],
            ['id' => 2, 'description' => 'Scope'],
            ['id' => 3, 'description' => 'Certainty'],
            ['id' => 4, 'description' => 'Relation'],
        ];
        \DB:: table($table) -> insert($entries);
    }
}
