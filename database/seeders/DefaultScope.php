<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DefaultScope extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table = 'scopes';
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table($table)->truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $entries = [
            ['id' => 1, 'description' => 'Everything', 'degree' => 1, 'comment' => ''],
            ['id' => 2, 'description' => 'All', 'degree' => 1, 'comment' => ''],
            ['id' => 3, 'description' => 'Almost All', 'degree' => 1, 'comment' => ''],
            ['id' => 4, 'description' => 'Most', 'degree' => 1, 'comment' => ''],
            ['id' => 5, 'description' => 'Some', 'degree' => 0, 'comment' => ''],
            ['id' => 6, 'description' => 'Few', 'degree' => 0, 'comment' => ''],
            ['id' => 7, 'description' => 'None', 'degree' => 0, 'comment' => ''],
            ['id' => 8, 'description' => 'Always', 'degree' => 1, 'comment' => ''],
            ['id' => 9, 'description' => 'Mostly', 'degree' => 1, 'comment' => ''],
            ['id' => 10, 'description' => 'Occasionally', 'degree' => 0, 'comment' => ''],
            ['id' => 11, 'description' => 'Generally', 'degree' => 1, 'comment' => ''],
            ['id' => 12, 'description' => 'Rarely', 'degree' => 0, 'comment' => ''],
            ['id' => 13, 'description' => 'Never', 'degree' => 0, 'comment' => ''],
            ['id' => 14, 'description' => 'Total', 'degree' => 1, 'comment' => ''],
            ['id' => 15, 'description' => 'Partial', 'degree' => 0, 'comment' => ''],
            ['id' => 16, 'description' => 'Substantial', 'degree' => 1, 'comment' => 'The measure of Substantial varies greatly by the context.'],
            ['id' => 17, 'description' => 'One', 'degree' => 0, 'comment' => ''],
            ['id' => 18, 'description' => 'All But One', 'degree' => 99, 'comment' => ''],
            ['id' => 19, 'description' => 'Half', 'degree' => 50, 'comment' => '']
        ];
        \DB:: table($table) -> insert($entries);
    }
}
