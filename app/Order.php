<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable=['name','business_name','number','email','adress','current_adress','payment_method','wallet_number','transaction','payment_ammount','note'];
}
