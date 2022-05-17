<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invpurchase extends Model
{
    protected $fillable=[
    	'dates','supplier_id','total_item','transport','labour_cost','total_payable','total','micro_time','user_id'
    ];
}
