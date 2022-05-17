<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable=[
    	'product_name','category','child_category','product_code','model_no','warranty','product_type','packaging','price','photo','user_id'
    ];
}
