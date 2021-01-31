<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PredefinedContext extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table = 'contexts';
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table($table)->truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $entries = [
            ['id' => 1, 'description' => 'general'],
            ['id' => 2, 'description' => 'survival'],
            ['id' => 3, 'description' => 'health'],
            ['id' => 4, 'description' => 'home'],
            ['id' => 5, 'description' => 'finance'],
            ['id' => 6, 'description' => 'science'],
            ['id' => 7, 'description' => 'family'],
            ['id' => 8, 'description' => 'children'],
            ['id' => 9, 'description' => 'art'],
            ['id' => 10, 'description' => 'leisure'],
            ['id' => 11, 'description' => 'communication'],
            ['id' => 12, 'description' => 'justice'],
            ['id' => 13, 'description' => 'community'],
            ['id' => 14, 'description' => 'city'],
            ['id' => 15, 'description' => 'nation'],
            ['id' => 16, 'description' => 'globe'],
            ['id' => 17, 'description' => 'planet'],
            ['id' => 18, 'description' => 'solar system'],
            ['id' => 19, 'description' => 'universe'],
            ['id' => 20, 'description' => 'philosophy'],
            ['id' => 21, 'description' => 'religion'],
            ['id' => 22, 'description' => 'spirituality'],
        ];
        \DB:: table($table) -> insert($entries);
    }
}
