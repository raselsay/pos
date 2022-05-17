<?php

use Illuminate\Database\Seeder;
use App\Customer;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Customer::class,100)->create();
    }
}
