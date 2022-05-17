<?php

use Illuminate\Database\Seeder;
use App\Voucer;
class VoucerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Voucer::class,10)->create();
    }
}
