<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable=[
    	'name','adress','capacity','type','status','user_id'
    ];
}
