<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Namerelation extends Model
{
    protected $fillable=['rel_name','name_id','user_id'];
}
