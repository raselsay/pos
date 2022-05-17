<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use DataTables;
class UserController extends Controller
{
    public function __construct(){
    	$this->middleware('auth');
    	$this->middleware('role:Super-Admin');
    }
    public function Form(){
    	if (request()->ajax()){
        $get=DB::select("
        	SELECT id,name,email from users
           ");
        return DataTables::of($get)
          ->addIndexColumn()
          ->addColumn('action',function($get){
          $button  ='<div class="btn-group btn-group-toggle" data-toggle="buttons">
                       <button type="button" data-id="'.$get->id.'" class="btn btn-sm btn-primary rounded mr-1 edit" data-toggle="modal" data-target=""><i class="fas fa-eye"></i></button>
                       <button class="btn btn-danger btn-sm rounded delete" data-id="'.$get->id.'"><i class="fas fa-trash-alt"></i></button>
                    </div>';
        return $button;
      })
      ->rawColumns(['action'])->make(true);
        }
       return view('pages.permission.user');
    }
    public function searchUser(Request $r){
      if (!preg_match("/[^a-zA-Z0-9. ]/", $r->searchTerm)) {
         $data = DB::select("SELECT id,name from users where name like '%" . $r->searchTerm . "%' limit 10");
         foreach ($data as $value) {
             $set_data[] = ['id' => $value->id, 'text' => $value->name];
         }

         if (isset($set_data)) {
            return response()->json($set_data);
         }else{
            return response()->json(['error'=>'not found']);
         }
     }
    }
}
