<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invpurchasebacks extends Model
{
    protected $fillable=[
    	'dates','supplier_id','total_item','transport','labour_cost','fine','total_payable','total','micro_time'
    ];
}
