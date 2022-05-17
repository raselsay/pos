<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable=[
    	'details','action','action_by','action_on'
    ];
}
