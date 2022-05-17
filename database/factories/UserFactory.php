<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Voucer;
use App\Information;
use App\Customer;
use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use App\Test;


/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/
function xxx(){
    $rand=rand(200,null);
    if($rand!=null){
        $x=$rand;
        $a=0;
        return [$x,$a];
    }else{
        $x=500;
        $a=0;
        return [$x,$a];
    }
}


$factory->define(Voucer::class, function (Faker $faker){
    
    return [
        'bank_id' => 1,
        'dates' => strtotime(date('d-m-Y')),
        'category' => 'customer',
        'data_id' => 19,
        'debit'=>xxx()[0],
        'credit'=>xxx()[1],
        'user_id'=>1,
    ];
});

$factory->define(Information::class, function (Faker $faker){
    return [
        'company_name' => 'Noman Enterprize',
        'company_slogan' => 'accounts for all',
        'country' => 'Bangladesh',
        'adress' => 'barisal,Bangladesh',
        'phone' => '01823767347',
        'logo'=>'fixed.jpg',
    ];
});

$factory->define(Customer::class, function (Faker $faker){
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->email,
        'phone1' => $faker->unique()->phoneNumber,
        'adress' => $faker->address,
        'stutus' =>1,
    ];
});

$factory->define(Test::class, function (Faker $faker){
    return [
        'name' =>$faker->unique()->name,
    ];
});

$factory->define(User::class, function (Faker $faker){
    return [
        'name' =>'Abduullah al Noman',
        'email' =>'noman.eng73@gmail.com',
        'password' =>Hash::make(12345678),
    ];
});
$factory->define(Role::class, function (Faker $faker){
    return [
        'name' =>'Super-Admin',
        'guard_name' =>'web',
    ];
});