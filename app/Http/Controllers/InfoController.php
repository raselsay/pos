<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Validator;
use DB;
use App\Information;
use Auth; 
use Storage;
class InfoController extends Controller
{
    public function __construct(){
    	$this->middleware('auth');
    }
    public function Form(){
        $res = Information::first();
        $res=$res->makeHidden(['created_at','updated_at']);
    	return view('pages.info.info_form',compact('res'));
    }
    public function Insert(Request $r,$id){
    	// return $r->all();
    	$validator=validator::make($r->all(),[
    		'company_name'=>'required|min:1|max:100',
    		'company_slogan'=>'required|min:1|max:100',
    		'adress'=>'required|min:1|max:100',
    		'phone'=>'required|max:50',
    		'email'=>'nullable|max:100',
            'city'=>'nullable|max:100',
    		'country'=>'required|max:100',
    		'state'=>'nullable|max:100',
    		'post_code'=>'nullable|max:100',
    		'stock_warning'=>'required|max:100',
    		'sms_api'=>'nullable|url|max:100',
    		'sms_sender'=>'nullable|max:100',
    		'sms_setting'=>'required|max:100',
    		'logo'=>'nullable|max:100',
    	]);

    	if ($validator->passes()) {
    		$information=Information::find($id);
    		$information->company_name=$r->company_name;
    		$information->company_slogan=$r->company_slogan;
    		$information->adress=$r->adress;
    		$information->phone=$r->phone;
            $information->email=$r->email;
    		$information->country=$r->country;
    		$information->city=$r->city;
    		$information->state=$r->state;
    		$information->post_code=$r->post_code;
    		$information->stock_warning=$r->stock_warning;
    		$information->sms_api=$r->sms_api;
    		$information->sms_sender=$r->sms_sender;
    		$information->sms_setting=$r->sms_setting;
    		$information->user_id=Auth::user()->id;
    		if ($r->hasFile('logo')) {
                $photo=DB::table('information')->select('logo')->where('id',$id)->first();
    			$ext=$r->logo->getClientOriginalExtension();
       			$name=Auth::user()->id.str_replace(' ','_',$r->company_name).time().'.'.$ext;
        		$r->logo->storeAs('public/logo',$name);
        		$information->logo=$name;
    		}else{
    			$information->logo='fixed.jpg';
    		}
    		$save=$information->save();
            if ($save) {
                if (isset($photo) and $photo->logo!='fixed.jpg') {
                   Storage::delete('public/logo/'.$photo->logo);
                }
                return ['message'=>'success','sms'=>$r->sms_setting];
            }
    	}
    	return response()->json([$validator->getMessageBag()]);
    }
}
