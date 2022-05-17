<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Delivery;
use App\Store;
use Validator;
use App\Dpermission;
use Auth;
use DB;
use DataTables;
class DpermissionController extends Controller
{
    public function __construct(){
    	$this->middleware('auth');
    }
    public function Form(){
    	$user=Delivery::all();    	
    	$store=Store::all();
         if (request()->ajax()) {
            $get = DB::select("
           select dpermissions.id,deliveries.name as del_admin,stores.name as store_name from dpermissions
           inner join deliveries on deliveries.id=dpermissions.delivery_id 
           inner join stores on stores.id=dpermissions.store_id
            ");
            return DataTables::of($get)
                ->addIndexColumn()
                ->addColumn('action', function ($get) {
                    $button = '<div class="btn-group btn-group-toggle" data-toggle="buttons">
                       <button type="button" data-id="' . $get->id . '" class="btn btn-sm btn-primary rounded mr-1 edit" data-toggle="modal" data-target=""><i class="fas fa-eye"></i></button>
                       <button class="btn btn-danger btn-sm rounded delete" data-id="' . $get->id . '"><i class="fas fa-trash-alt"></i></button>
                    </div>';
                    return $button;
                })
                ->rawColumns(['action'])->make(true);
        }
       return view('pages.dpermission.permission',compact('user','store'));
    }
    public function Create(Request $r){
 		$validator = Validator::make($r->all(), [
            'store' => 'required|max:25',            
            'delivery' => 'required|max:25',
        ]);
        //for image
        if ($validator->passes()) {
            $dpermission = new Dpermission;
            $dpermission->delivery_id = $r->delivery;            
            $dpermission->store_id = $r->store;
            $dpermission->user_id = Auth::user()->id;
            $dpermission->save();
            return response()->json(['message' => 'Category Added Success']);
        }
        return response()->json($validator->getMessageBag());
    }
}
