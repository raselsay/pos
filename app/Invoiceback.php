<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoiceback extends Model
{
    Protected $fillable=[
    	'dates','customer_id','total_item','fine','total_payable','total','micro_time'
    ];
}
