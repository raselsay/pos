<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Voucer extends Model
{
    protected $fillable=[
    	'bank_id','dates','nickname','category','data_id','payment_type','ammount','micro_time','user_id'
    ];
}
