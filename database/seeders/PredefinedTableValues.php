<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PredefinedTableValues extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(DefaultOpinionAttribute::class);
        $this->call(DefaultRelationType::class);
        $this->call(DefaultStatementCertainty::class);
        $this->call(DefaultStatementType::class);
    }
}
