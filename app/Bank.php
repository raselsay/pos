<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $fillable=[
   		'name', 'number', 'branch','opening_balance','users_id'
    ];
}
