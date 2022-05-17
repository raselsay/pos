<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Validator;
use Auth;
use DataTables;
use App\Ptype;
use App\Notification;
class ProductTypeController extends Controller
{
    public function __construct(){
    	$this->middleware('auth');
    }
    public function ManageProductType(){
    	if (request()->ajax()) {
        $get=DB::select("select ptypes.id,ptypes.name,users.id as user_id,users.name as username from ptypes inner join users on users.id=ptypes.user_id");
        return DataTables::of($get)
          ->addIndexColumn()
          ->addColumn('username',function($get){
            return $get->username.'('.$get->user_id.')';
          })
          ->addColumn('action',function($get){
          $button  ='<div class="btn-group btn-group-toggle" data-toggle="buttons">
                       <button type="button" data-id="'.$get->id.'" class="btn btn-sm btn-primary rounded mr-1 edit" data-toggle="modal" data-target=""><i class="fas fa-eye"></i></button>
                       <button class="btn btn-danger btn-sm rounded delete" data-id="'.$get->id.'"><i class="fas fa-trash-alt"></i></button>
                    </div>';
        return $button;
        })
        ->rawColumns(['username','action'])->make(true);
        }
       return view('pages.products.product_type');
    }
    public function insertProductType(Request $r){
    	$validator=Validator::make($r->all(),[
    		"product_type"=>'required|max:100|min:1',
    	]);

    	if ($validator->passes()) {
    		$type=new Ptype;
    		$type->name=$r->product_type;
    		$type->user_id=Auth::user()->id;
    		$type->save();
    		return ['message'=>'Product Type Added Success'];
    	}
    return response()->json($validator->getMessageBag());
    }
    public function Delete($id=null){
        $name=Ptype::where('id',$id)->select('name')->first();
        $delete=Ptype::where('id',$id)->delete();
        if ($delete) {
            $notification=new Notification;
            $notification->details='Product Type <strong>'.$name->name.'('.$id.')</strong>'.' Deleted by <strong>'.Auth::user()->name.'('.Auth::user()->id.')</strong>';
            $notification->action='delete';
            $notification->save();
            return response()->json(['message'=>'success']);
        }
    }
    public function getPtype($id=null){
        $data=DB::table('ptypes')->select('name')->where('id',$id)->first();
        return response()->json($data);
    }
    public function Update(Request $r,$id=null){
        $validator=Validator::make($r->all(),[
            "product_type"=>'required|max:100|min:1',
        ]);
        if ($validator->passes()) {
            $type=Ptype::find($id);
            $type->name=$r->product_type;
            $type->user_id=Auth::user()->id;
            $save=$type->save();
            if ($save){
                $notification=new Notification;
                $notification->details='Product Type <strong>'.$type->name.'('.$id.')</strong>'.' Updated by <strong>'.Auth::user()->name.'('.Auth::user()->id.')</strong>';
                $notification->action='update';
                $notification->save();
                return ['message'=>'success'];
            }
            
        }
    return response()->json($validator->getMessageBag());
    }
}
