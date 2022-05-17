<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WarehousePermission extends Model
{
    protected $fillable=[
        'user_id','store_id'
    ];
}
