<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DefaultStatementType extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table = 'statement_types';
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table($table)->truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $entries = [
            ['id' => 1, 'description' => 'Claims', 'explaination' => 'A claim is a statement which is the conclusion of an argument supported or contradicted by the contributions of the community.'],
            ['id' => 2, 'description' => 'Terms', 'explaination' => 'A term is an label created for use in a specific domain of expertise. Terms contain essential and non-essential inclusions and exclusions.'],
            ['id' => 3, 'description' => 'Definition', 'explaination' => 'A definition defines a word or phrase as it is commonly used in a certain context. Definitions contain essential and non-essential inclusions and exclusions.'],
            ['id' => 4, 'description' => 'Question', 'explaination' => 'A question is a statement requesting an answer.'],
        ];
        \DB:: table($table) -> insert($entries);
    }
}
