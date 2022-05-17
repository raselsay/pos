<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChildCategory extends Model
{
    protected $fillable=[
    	'cat_id','name', 'user_id'
    ];
}
