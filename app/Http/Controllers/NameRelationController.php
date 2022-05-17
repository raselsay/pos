<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use DataTables;
use Validator;
use Auth;
use App\Namerelation;
use App\Name;
class NameRelationController extends Controller
{
    public function __construct(){
    	$this->middleware('auth');
    }

    public function ManageNameRelation(){
    	$names=DB::table('names')->select('id','name')->where('table_name',1)->get();
    	if (request()->ajax()) {
        $get=DB::select("select namerelations.id,namerelations.rel_name as rel_name,users.id as user_id,users.name as username,names.name as name from namerelations left join users on users.id=namerelations.user_id 
            inner join names on names.id=namerelations.name_id");
          return DataTables::of($get)
          ->addIndexColumn()
          ->addColumn('username',function($get){
          	$username=$get->username.'('.$get->user_id.')';
          	return $username;
          })
          ->rawColumns(['username'])->make(true);
        }
        return view('pages.names.namerelation',compact('names'));
    }
    public function insertNameRelation(Request $r){
    	$validator=Validator::make($r->all(),[
    		'rel_name'=>'required|max:100',
    		'name'=>'required|max:10',
    	]);

    	if ($validator->passes()) {
    		$nameRelation=new Namerelation;
    		$nameRelation->rel_name=ucwords($r->rel_name);
    		$nameRelation->name_id=$r->name;
    		$nameRelation->user_id=Auth::user()->id;
    		$nameRelation->save();
    		return ['message'=>'Account Head Added'];
    	}
    	return [$validator->getMessageBag()];
    }

    public function getRelationById($id=null,Request $r){
        $data=Name::select('table_name')->where('id',$id)->first();
        if (is_string($data->table_name) && $data->table_name!=1) {
                  $table=DB::table('names')
                        ->select('table_name')
                        ->where('id',$id)
                        ->first();
          $fields=DB::select("
            SHOW Fields from ".$table->table_name."
            ");
          foreach($fields as $field){
            if($field->Field==='phone' or $field->Field==='phone1'){
              $fieldName=$field->Field;
            }
            if ($field->Field=='adress') {
              $adress=$field->Field;
            }
          }
          $data=DB::select("SELECT id,name,".((isset($fieldName)) ? $fieldName : '' ).",".((isset($adress)) ? $adress : '' )." from ".$table->table_name." where name like :term or ".$fieldName." like :term limit 10",['term'=>'%'.$r->searchTerm.'%']);
        }else{
            $data=Namerelation::select('id','rel_name as name')->where('name_id',$id)->get();
        }
         foreach ($data as $value){
            $set_data[]=['id'=>$value->id,'text'=>$value->name.((isset($fieldName) && $value->$fieldName) ? '('.$value->$fieldName.')' : '').((isset($adress) && $value->$adress!=null) ? '-'.$value->$adress : '')];
         }
         if (isset($set_data)){
           return $set_data;
         }else{
           return response()->json(['message'=>'not found']);
         }
      }
}
