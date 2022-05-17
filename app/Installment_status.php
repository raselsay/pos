<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Installment_status extends Model
{
    protected $fillable=['invoice_id','dates','last_date','date_over_fine','voucer_id','status'];
}
