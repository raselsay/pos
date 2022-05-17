<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Store;
use App\User;
use App\WarehousePermission;
use Validator;
class WarehousePermissionController extends Controller
{   
    public function __construct(){
        $this->middleware('auth');
    }
    public function Form(){
        $store=Store::select('id','name')->get();
        $user=User::select('id','name')->get();
        return view('pages.warehousePermission.permission',compact('store','user'));
    }
    public function Create(Request $r){
        $validator=Validator::make($r->all(),[
            'user'=>'required|max:15|unique:warehouse_permissions,user_id',
            'store'=>'required|max:15',
        ]);
        
        if ($validator->passes()) {
            $permission= new WarehousePermission;
            $permission->user_id=intval($r->user);
            $permission->store_id=intval($r->store);
            $permission->save();
            if ($permission) {
                return response()->json(['message'=>'Warehouse Permission Added Success']);
            }
        }
        return response()->json($validator->getMessageBag());
    }
}
