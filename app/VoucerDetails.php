<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VoucerDetails extends Model
{
    protected $fillable=[
    	'voucer_id','details','qantity','ammount','user_id'
    ];
}
