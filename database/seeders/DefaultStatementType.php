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
            ['id' => 1, 'description' => 'Claims', 'explanation' => 'A claim is a statement which is the conclusion of an argument supported or contradicted by the contributions of the community.', 'color' => '#DFF0D8', 'support_label' => 'Supports', 'counter_label' => 'Counters'],
            ['id' => 2, 'description' => 'Terms', 'explanation' => 'A term is an label created for use in a specific domain of expertise. Terms contain essential and non-essential inclusions and exclusions.', 'color' => '#F0D8EA', 'support_label' => 'Inclusions', 'counter_label' => 'Exclusions'],
            ['id' => 3, 'description' => 'Definition', 'explanation' => 'A definition defines a word or phrase as it is commonly used in a certain context. Definitions contain essential and non-essential inclusions and exclusions.', 'color' => '#D8EDF0', 'support_label' => 'Inclusions', 'counter_label' => 'Exclusions'],
            ['id' => 4, 'description' => 'Question', 'explanation' => 'A question is a statement requesting an answer.', 'color' => '#F6F589', 'support_label' => 'Answers', 'counter_label' => 'Moots'],
            ['id' => 5, 'description' => 'Risk', 'explanation' => 'A claim about a possible future event.', 'color' => '#FFC6c6', 'support_label' => 'Rewards', 'counter_label' => 'Damages'],
            ['id' => 6, 'description' => 'Hearsay', 'explanation' => 'Subjective testimony about something you\'ve heard.', 'color' => '#FEB0b3', 'support_label' => 'Supports', 'counter_label' => 'Counters'],
            ['id' => 7, 'description' => 'Subjective', 'explanation' => 'Testimony about your subjective experience s and preferences. ', 'color' => '#CDDF76', 'support_label' => 'Supports', 'counter_label' => 'Counters'],
            ['id' => 8, 'description' => 'Debate', 'explanation' => 'A debate is an adverserial presentation for and against a claim or question.', 'color' => '#CDDF76', 'support_label' => 'For', 'counter_label' => 'Against'],
            ['id' => 9, 'description' => 'Quality', 'explanation' => 'A quality is a statement with focus on functionality and desirability.', 'color' => '#F6F589', 'support_label' => 'Pros', 'counter_label' => 'Cons'],
        ];
        \DB:: table($table) -> insert($entries);
    }
}
