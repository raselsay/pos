<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Keycheck;
class LisencekeyController extends Controller
{
    public function __construct(){
    	// $this->middleware('keycheck');
    }
    public function Form(){
    	if ($this->check_internet('www.google.com')) {
    		$todate=Keycheck::select('todate')->first();
			$unixtime=$this->getResponse();
			$internetdate=intval(strtotime(strval(date('d-m-Y',intval($unixtime)))));
			if (intval($todate->todate)<intval($internetdate)) {
				return view('auth.lisencekey');
			}else{
				return "<h3 style='margin-top:100px;text-align:center;color:red;'>You Don't Have Permission For This Page</h3>";
			}
		}else{
			return redirect()->back()->with(['message'=>'Internet Connection Failed! Please Check Your Internet Connection']);
		}
    }
    public function Create(Request $r){
    	// return $r->all();
    	$validator=Validator::make($r->all(),[
    		'field_1'=>'required|max:5|min:5|regex:/^([a-zA-Z0-9]+)$/',
    		'field_2'=>'required|max:5|min:5|regex:/^([a-zA-Z0-9]+)$/',
    		'field_3'=>'required|max:5|min:5|regex:/^([a-zA-Z0-9]+)$/',
    		'field_4'=>'required|max:5|min:5|regex:/^([a-zA-Z0-9]+)$/',
    	]);
    	if ($validator->passes()){
    		$todate=Keycheck::select('todate')->first();
    		if ($this->check_internet('www.google.com')) {
    			$unixtime=$this->getResponse();
    			$internetdate=intval(strtotime(strval(date('d-m-Y',intval($unixtime)))));
    		}
    		$monthly=strtoupper(substr(md5("monthly+100"),27,5));
	    	$monthly6=strtoupper(substr(md5('6monthly+600'),27,5));
	    	$yearly=strtoupper(substr(md5('yearly+1000'),27,5));
	    	$today=strtoupper(substr(md5(strtotime(strval(date('d-m-Y',$unixtime)))),27,5));
	    	$secret=strtoupper(substr(md5((185*strtotime(date('d-m-Y',$unixtime)))/10),27,5));
	    	$secret2=strtoupper(substr(md5((13*500)/intval($id=1002398)),27,5));
	    	// return $today;
	    	switch (true) {
	    		case $monthly==$r->field_1:
	    			$firstkey=$monthly;
	    			$month=1;
	    			break;
	    		case $monthly6==$r->field_1:
	    			$firstkey=$monthly6;
	    			$month=6;
	    			break;
	    		case $yearly==$r->field_1:
	    			$firstkey=$yearly;
	    			$month=12;
	    			break;
	    	}
	    	if ($firstkey.$today.$secret.$secret2==$r->field_1.$r->field_2.$r->field_3.$r->field_4)
	    	{
	    		if (isset($todate->todate) and intval($internetdate)>intval($todate->todate)){
	    			$update=Keycheck::first();
	    			$update->fromdate=$todate->todate;
	    			$update->todate=strtotime( "+".$month." month",strtotime(date('d-m-Y',$unixtime)));
	    			$update->key=$r->field_1.'-'.$r->field_2.'-'.$r->field_3.'-'.$r->field_4;
	    			$update->status=true;
	    			$update->save();
	    			return redirect('/login')->with(['message'=>'Key Matched Try To Login Again']);
	    			}else if(intval($internetdate)>intval($todate->todate)){
	    			$update=Keycheck::first();
	    			$update->fromdate=$internetdate;
	    			$update->todate=strtotime( "+".$month." month",strtotime(date('d-m-Y',$unixtime)));
	    			$update->key=$r->field_1.'-'.$r->field_2.'-'.$r->field_3.'-'.$r->field_4;
	    			$update->status=true;
	    			$update->save();
	    			return redirect('/login')->with(['message'=>'Key Matched Try To Login Again']);
	    		}
	    		
	    	}else{
	    		return redirect()->back()->with(['error'=>'Key Not Matched']);
	    	}
	    }
	    return redirect()->back()->with(['error'=>'Invalid Key']);
    }
    private function check_internet($domain){
        $file = @fsockopen ($domain, 80);//@fsockopen is used to connect to a socket
        // return true;
        return ($file);
        
    }
    private function getResponse(){
        $defaults = array(
            CURLOPT_URL             => 'http://worldtimeapi.org/api/timezone/Asia/Dhaka',
            CURLOPT_POST            => false,
            CURLOPT_HEADER          => false,
            CURLOPT_SSL_VERIFYPEER  => false,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_SSL_VERIFYHOST  => false,
            );
            $curl               = curl_init();
            curl_setopt_array($curl, $defaults);
            $curl_response      = curl_exec($curl);
            $json_object       = json_decode($curl_response);
            // var_dump(curl_error($curl));
            curl_close($curl);
            return $json_object->unixtime;
    }
}
