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
            ['id' => 1, 'description' => 'Claims', 'explaination' => 'A claim is a statement which is the conclusion of an argument supported or contradicted by the contributions of the community.', 'color' => '#DFF0D8'],
            ['id' => 2, 'description' => 'Terms', 'explaination' => 'A term is an label created for use in a specific domain of expertise. Terms contain essential and non-essential inclusions and exclusions.', 'color' => '#F0D8EA'],
            ['id' => 3, 'description' => 'Definition', 'explaination' => 'A definition defines a word or phrase as it is commonly used in a certain context. Definitions contain essential and non-essential inclusions and exclusions.', 'color' => '#D8EDF0'],
            ['id' => 4, 'description' => 'Question', 'explaination' => 'A question is a statement requesting an answer.', 'color' => '#F6F589'],
            ['id' => 5, 'description' => 'Risk', 'explaination' => 'A claim about a possible future event.', 'color' => '#FFC6c6'],
            ['id' => 6, 'description' => 'Hearsay', 'explaination' => 'Subjective testimony about something you\'ve heard.', 'color' => '#FEB0b3'],
            ['id' => 7, 'description' => 'Subjective', 'explaination' => 'Testimony about your subjective experience s and preferences. ', 'color' => '#CDDF76'],
        ];
        \DB:: table($table) -> insert($entries);
    }
}
