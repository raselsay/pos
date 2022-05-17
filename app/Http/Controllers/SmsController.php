<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
class SmsController extends Controller
{
    public function __construct(){
    	$this->middleware('auth');
    }
    public function Dashboard(){
    	// return 'pok';
    	$data=DB::table('information')->select('sms_api','sms_sender')->first();
    	return view('pages.sms.sms_dashboard',compact('data'));
    }
    public function CustomSms(){
    	// return 'pok';
    	$data=DB::table('information')->select('sms_api','sms_sender')->first();
    	return view('pages.sms.custom_sms',compact('data'));
    }
    public function getNumber($id){
    	switch ($id) {
    		case 0:
    			$data=DB::table('customers')->select('phone1')->get();
    			break;
    		case 1:
    			$data=DB::table('suppliers')->select('phone')->get();
    			break;
    		case 2:
    			$data=DB::table('employees')->select('phone')->get();
    			break;
    	}
    	if (isset($data)) {
    		$num='';
    		foreach($data as $numbers){
	    		if ($id==0) {
	    			$num.=$numbers->phone1.',';
	    		}else{
	    			$num.=$numbers->phone.',';
	    		}
    		}
    	}else{
    		$num=[];
    	}
    	return response()->json($num);
    }
}
