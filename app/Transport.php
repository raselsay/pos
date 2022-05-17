<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transport extends Model
{
    protected $fillable=['name','phone','driver_phone','adress','type','status','user_id'];
}
