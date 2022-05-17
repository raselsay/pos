<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable=[
    	'name', 'email', 'phone','adress','experience','nid','salary','job_department','city','photo','users_id'
    ];
}
