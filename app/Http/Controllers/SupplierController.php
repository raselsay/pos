<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Validator;
use DataTables;
use DB;
use App\Supplier;
use App\Notification;
class SupplierController extends Controller
{
    public function __construct(){
    	$this->middleware('auth');
    }
    public function ManageSupplier(){
    	if (request()->ajax()) {
            $total_bal=500;
            $get=DB::select("select id,name,phone,adress,supplier_type from suppliers order by id desc");
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
        return view('pages.Supplier.supplier');
    }
    public function insertSupplier(Request $r){
        if($r->opening_balance===null){
            $r->opening_balance=0;
        }
    	$validator = Validator::make($r->all(),[
        'name'       		=> "required|max:50",
        'email'     		=> 'nullable|max:30|email|unique:suppliers,email',
        'phone'     		=> 'required|max:20',
        'opening_balance'   => 'nullable|max:19|regex:/^([0-9.]+)$/',
        'balance_type'      => 'required|max:1|regex:/^([0-1]+)$/',
        'adress'    		=> 'required|max:100',
        'supplier_type'     => 'required|max:50'
        ]);

    //for image
    if ($validator->passes()){
    	$supplier= new Supplier;
        $supplier->name              = $r->name;
        $supplier->email             = $r->email;
        $supplier->phone             = $r->phone;
        $supplier->adress   		 = $r->adress;
        if($r->balance_type!==null){
          switch($r->balance_type){
              case '0':
                $supplier->opening_balance=-abs($r->opening_balance);
              break;
              case '1':
                $supplier->opening_balance=abs($r->opening_balance);
              break;
          }
        }
        
        $supplier->supplier_type   	 = $r->supplier_type;
        $supplier->users_id   		 = Auth::user()->id;
        $supplier->save();
        return response()->json(['message'=>'success']);
    }
    return response()->json([$validator->getMessageBag()]);
    }
    public function DeleteSupplier($id){
        return "sorry you dont have to permisson for delete.";
        $supplierName=DB::table('suppliers')->select('name')->where('id',$id)->first();
    	$delete=Supplier::where('id',$id)->delete();
        if ($delete) {
            $notification=new Notification;
            $notification->details='supplier <strong>'.$supplierName->name.'('.$id.')</strong>'.' deleted by <strong>'.Auth::user()->name.'('.Auth::user()->id.')</strong>';
            $notification->action='delete';
            $notification->save();
        }
    	return response()->json(['message'=>'success']);
    }
    public function UpdateSupplier($id,Request $r){
        // return $id;
        $validator = Validator::make($r->all(),[
        'name'              => "required|max:50",
        'email'             => 'nullable|max:30|email|unique:suppliers,email,'.$id,
        'phone'             => 'required|max:20|regex:/^([0-9]+)$/',
        'adress'            => 'required|max:100',
        'supplier_type'     => 'required|max:50'
        ]);

    //for image
    if ($validator->passes()){
        $supplier= Supplier::find($id);
        $supplier->name              = $r->name;
        $supplier->email             = $r->email;
        $supplier->phone             = $r->phone;
        $supplier->adress            = $r->adress;
        $supplier->supplier_type     = $r->supplier_type;
        if($r->balance_type!==null){
          switch($r->balance_type){
              case 0:
                $supplier->opening_balance=-abs($r->opening_balance);
              break;
              case 1:
                $supplier->opening_balance=abs($r->opening_balance);
              break;
            }
        }
        $supplier->users_id          = Auth::user()->id;
        $supplier->save();
        return response()->json(['message'=>'success']);
    }
    return response()->json([$validator->getMessageBag()]);
    }

    public function searchSupplier(Request $r){
            $data=DB::select("SELECT id,name,phone from suppliers where name like :term or  phone like :term limit 10",['term'=>'%'.$r->searchTerm.'%']);
            foreach ($data as $value) {
                $set_data[]=['id'=>$value->id,'text'=>$value->name.'('.$value->phone.')'];
            }
            if (isset($set_data)) {
                return $set_data;
            }
            
    }
    public function getSupplier($id){
        $data=DB::table('suppliers')->select('name','email','phone','adress','supplier_type','opening_balance')->where('id',$id)->first();
        return response()->json([$data]);
    }
    public function getBalance($id){
        $blnce=DB::select("
            SELECT 
((t.total_purchase+t.Deposit)-(t.total_purchase_backs+t.Expence))+opening_balance as total
from(
    SELECT
	names.name,
    ifnull((select sum(total_payable) from invpurchases where supplier_id=:id and action_id=0),0) as total_purchase,
    ifnull((select sum(total_payable) from invpurchases where supplier_id=:id and action_id=2),0) as total_purchase_backs,
    (SELECT opening_balance from suppliers where id=:id) as opening_balance,
    ifnull(sum(ifnull(voucers.debit,0)),0) as Deposit,
    ifnull(sum(ifnull(voucers.credit,0)),0) as Expence
    from voucers 
    left join names on names.id=voucers.category  where (voucers.data_id=:id and voucers.nickname='supplier') or (voucers.data_id=:id and names.table_name='suppliers')) t ",['id'=>$id]);
    return response()->json(['total'=>$blnce[0]->total]);
    }
}
