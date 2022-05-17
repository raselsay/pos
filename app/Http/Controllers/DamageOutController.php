<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Invpurchase;
use App\Purchase;
use Auth;
use DB;
use DataTables;
use URL;
use App\Notification;
class DamageOutController extends Controller
{
    public function __construct(){
    	$this->middleware('auth');
    }

    public function Form(){
    	return view('pages.purchase.damage_out');
    }
    public function Create(Request $r){
    	$data['product']=explode(',',$r->product[0]);      
        $data['store']=explode(',',$r->store[0]);
        $data['qantities']=explode(',',$r->qantities[0]);
        $data['prices']=explode(',',$r->prices[0]);
        $data['date']=strtotime(strval($r->date));
        $data['total_item']=$r->total_item;
        $validator=Validator::make($data,[ 
            'product'=>'required|array',
            'product.*'  =>'required|distinct|regex:/^([0-9]+)$/',
            'store'=>'required|array',
            'store.*'  =>'required|regex:/^([0-9]+)$/',
            'qantities'=>'required|array',
            'qantities.*'=>'required|regex:/^([0-9]+)$/',
            'prices'=>'required|array',
            'prices.*'=>'required|regex:/^([0-9]+)$/',
            'date'=>'required|max:10',
            'total_item'=>'required|max:10',
        ]);
        if ($validator->passes()) {
            $invoice=new Invpurchase;
            $invoice->dates=$data['date'];
            $invoice->total_item=$data['total_item'];
            $invoice->action_id=5;
            $invoice->user_id=Auth::user()->id;
            $invoice->save();
            $inv_id=$invoice->id;
            $user_id=$invoice->user_id;
            if ($invoice=true){
                    $length=intval($data['total_item'])-1;
                for ($i=0; $i <=$length; $i++){
                    $stmt=new Purchase();
                    $stmt->invoice_id=$inv_id;
                    $stmt->dates=$data['date'];
                    $stmt->product_id=$data['product'][$i];
                    $stmt->store_id=$data['store'][$i];
                    $stmt->cred_qantity=$data['qantities'][$i];
                    $stmt->price=$data['prices'][$i];
                    $stmt->user_id=$user_id;
                    $stmt->action_id=5;
                    $stmt->save();
                }
                if ($stmt=true){
                    return ['message'=>'Opening Stock Added Success'];
                }
            }
        }
        return response()->json([$validator->getMessageBag()]);
    }
    public function AllDamageOut() {
        if (request()->ajax()) {
            $get = DB::table('invpurchases')
                ->select('invpurchases.id', 'invpurchases.dates','invpurchases.action_id')
                ->where('invpurchases.action_id',5)
                ->orderByRaw('invpurchases.id desc')
                ->get();
            return DataTables::of($get)
                ->addIndexColumn()
                ->addColumn('action', function ($get) {
                    $button = '<div class="btn-group btn-group-toggle" data-toggle="buttons">
                       <a type="button" href="' . URL::to('admin/purchase-update') . '/' . $get->id . '" class="btn btn-sm btn-primary rounded mr-1 edit"><i class="fas fa-eye"></i></a>
                       <a class="btn btn-danger btn-sm rounded mr-1 delete" data-id="' . $get->id . '"><i class="fas fa-trash-alt"></i></a>
                       <a class="btn btn-secondary btn-sm rounded print" data-id="' . $get->id . '"><i class="fas fa-print"></i></a>
                    </div>';
                    return $button;
                })
                 ->addColumn('products', function ($get) {
                    $product_name='';

                    $sales = DB::select("
                    SELECT products.product_name,stores.name,(purchases.deb_qantity-purchases.cred_qantity) qantity FROM purchases
                    inner join stores on stores.id=purchases.store_id 
                    inner join products on products.id=purchases.product_id
                    where purchases.invoice_id=:id
                        ",['id'=>$get->id]);
                    foreach($sales as $sale){
                        $product_name.=$sale->product_name.'('.$sale->name.')'.'('.$sale->qantity.') ';
                    }
                    return $product_name;
                })
                ->addColumn('dates', function ($get) {
                    $date = date('d-m-Y', $get->dates);
                    return $date;
                })
                ->rawColumns(['action', 'dates'])->make(true);
        }
        return view('pages.purchase.all_damage_out');
    }
    public function Delete($id){
        $data=Invpurchase::find($id);
        if ($data['action_id']==5) {
            $data->delete();
            if ($data){
                $notification = new Notification;
                $notification->details = 'Damage Out No <strong>' . $id . '</strong>' . ' deleted by <strong>' . Auth::user()->name . '(' . Auth::user()->id . ')</strong>';
                $notification->action = 'delete';
                $save = $notification->save();
                if ($save) {
                    return response()->json(['message' => 'Damage Out Deleted Success']);
                }
            }
        }
    }
}
