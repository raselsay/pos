<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeSalaries extends Model
{
    protected $fillable=[
    	'dates', 'month', 'income_tax','medical','p_fund','basic','bonus','over_time','payable','user_id'
    ];
}
