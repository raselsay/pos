<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Keycheck extends Model
{
    protected $fillable=['fromdate','todate','key','status'];
}
