<?php

namespace App\Http\Controllers\setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\MultiSetting;
class MultiSettingController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    public function Form(){
        return view('pages.setting.multi_setting');
    }
    public function Create(Request $r){
        $length= count($r->all());
        $data=$r->all();
        foreach($data as $key => $value){
            $setting =MultiSetting::updateOrCreate(
                ['name'=>$key],
                ['value'=>$value],
            );
        }
        return response()->json(['message'=>'Setting Updated Success']);
    }

    public function GetData(){
        $data=MultiSetting::select('name','value')->get();
        return $data;
    }
}
