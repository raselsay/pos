<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable=[
    	'dates','supplier_id','product_id','qantity','price','micro_time'
    ];
}