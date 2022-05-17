<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
     protected $fillable=[
    	'company_name','name','phone1','phone2', 'email','birth_date','marriage_date', 'adress','city','postal_code','stutus','group','photo','users_id'
    ];
}
