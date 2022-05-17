<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Name extends Model
{
    protected $fillable=[
    	'name','user_id'
	];
}
