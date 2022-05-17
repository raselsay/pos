<?php

use Illuminate\Database\Seeder;
use App\Information;
class CompanyInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Information::class,1)->create();
    }
}
