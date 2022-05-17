<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use DataTables;
use Validator;
use App\Name;
class NameController extends Controller
{
    public function __construct(){
    	$this->middleware('auth');
    }
    public function ManageName(){
      $tables=DB::table('tables')->select('name','table_name')->get();
    	if (request()->ajax()) {
        $get=DB::select("select names.id,names.name,users.id as user_id,users.name as username from names left join users on users.id=names.user_id");
          return DataTables::of($get)
          ->addIndexColumn()
          ->addColumn('username',function($get){
          	$username=$get->username.'('.$get->user_id.')';
          	return $username;
          })
          ->rawColumns(['username'])->make(true);
        }
        return view('pages.names.name',compact('tables'));
    }
    public function insertName(Request $r){
    	$validator=Validator::make($r->all(),[
        'name'=>'required|max:100|min:1',
        'rel_id'=>'nullable|max:100|min:1',
      ]);
      if ($validator->passes()) {
        $name=new Name;
        $name->name=$r->name;
        if ($r->rel_id!=null) {
          $name->table_name=$r->rel_id;
        }
        $name->user_id=Auth::user()->id;
        $name->save();
        return response()->json(['message'=>'Account Name Added']);
      }
      return response()->json([$validator->getMessageBag()]);
    }
    public function searchName(Request $r){
         if (!preg_match("/[^a-zA-Z0-9. ]/", $r->searchTerm)) {
            $data=DB::select("SELECT id,name from names where name like '%".$r->searchTerm."%' limit 10");
            foreach ($data as $value) {
                $set_data[]=['id'=>$value->id,'text'=>$value->name];
            }
            return $set_data;
        }
    }
    
}
