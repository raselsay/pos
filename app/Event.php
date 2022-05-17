<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['customer_id','date','issue_date','description','status','user_id'];
}
