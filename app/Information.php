<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Information extends Model
{
    Protected $fillable=[
    	'company_name','company_slogan','adress','phone','email','city','state','post_code','stock_warning','sms_api','sms_sender','sms_setting','logo','user_id'
    ];
}
