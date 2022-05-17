<?php

namespace App\Http\Controllers;
use App\Invpurchase;
use App\Purchase;
use App\Voucer;
use App\Notification;
use Auth;
use DataTables;
use DB;
use Illuminate\Http\Request;
use URL;
use Validator;

class PurchaseController extends Controller {
    public function __construct() {
        $this->middleware('auth');
    }
    public function ManagePurchase() {
        return view('pages.purchase.purchase');
    }
    public function insertPurchase(Request $r) {
        // return $r->all();
        $data['product'] = explode(',', $r->product[0]);       
        $data['store'] = explode(',', $r->store[0]);
        $data['qantities'] = explode(',', $r->qantities[0]);
        $data['prices'] = explode(',', $r->prices[0]);
        $data['supplier'] = $r->supplier;
        $data['date'] = strtotime(strval($r->date));
        $data['issue_date'] = strtotime(strval($r->issue_date));
        $data['total_payable'] = $r->total_payable;
        $data['total_item'] = $r->total_item;
        if ($r->transport==='null') {
            $data['transport_id'] = null;
        }else{
            $data['transport_id'] = $r->transport;
        }
        if (isset($r->transport_cost)) {
            $data['transport'] = $r->transport_cost;
        } else {
            $data['transport'] = null;
        }
        $data['purchase_type'] = $r->purchase_type;
        if (isset($r->labour)) {
            $data['labour'] = $r->labour;
        } else {
            $data['labour'] = null;
        }
        $data['transaction'] = $r->transaction;
        $data['payment_method'] = $r->payment_method;
        if ($r->payment_method == 'null') {
            $data['payment_method'] = null;
        }
        $data['pay'] = $r->pay;
        $data['total'] = $r->total;
        $data['note'] = $r->note;
        // return $r->all();
        $validator = Validator::make($data, [
            'product' => 'required|array',
            'product.*' => 'required|distinct|regex:/^([0-9]+)$/',
            'qantities' => 'required|array',
            'qantities.*' => 'required|max:15|regex:/^([0-9.]+)$/',
            'prices' => 'required|array',
            'prices.*' => 'required|max:15|regex:/^([0-9.]+)$/',
            'purchase_type' => 'required|regex:/^([0-2]+)$/',
            'supplier' => 'required|regex:/^([0-9]+)$/',
            'date' => 'required|max:10',
            'issue_date' => 'required|max:10',
            'total_payable' => 'required|max:10|regex:/^([0-9.]+)$/',
            'total_item' => 'required|max:10|regex:/^([0-9]+)$/',
            'transport' => 'nullable|max:15|regex:/^([0-9]+)$/',
            'payment_method' => 'nullable|max:15|regex:/^([0-9]+)$/',
            'transaction' => 'nullable|max:15|regex:/^([a-zA-Z0-9]+)$/',
            'pay' => 'nullable|max:15|regex:/^([0-9.]+)$/',
            'transport_cost' => 'nullable|max:15|regex:/^([0-9.]+)$/',
            'labour' => 'nullable|max:15|regex:/^([0-9.]+)$/',
            'total' => 'required|max:15|regex:/^([0-9.]+)$/',
            'note' => 'nullable|max:500',
        ]);
        if ($validator->passes()) {
            $invoice = new Invpurchase;
            $invoice->dates = $data['date'];
            $invoice->issue_date = $data['issue_date'];
            $invoice->supplier_id = $data['supplier'];
            $invoice->total_item = $data['total_item'];
            $invoice->transport = $data['transport'];
            $invoice->transport_id = $data['transport_id'];
            $invoice->labour_cost = $data['labour'];
            $invoice->total_payable = $data['total_payable'];
            $invoice->total = $data['total'];
            $invoice->action_id = $data['purchase_type'];
            $invoice->user_id = Auth::user()->id;
            $invoice->note = $data['note'];
            $invoice->save();
            $inv_id = $invoice->id;
            $user_id = $invoice->user_id;
            if ($invoice = true) {
                $length = intval($data['total_item']) - 1;
                for ($i = 0; $i <= $length; $i++) {
                    $stmt = new Purchase();
                    $stmt->invoice_id = $inv_id;
                    $stmt->dates = $data['date'];
                    $stmt->supplier_id = $r->supplier;
                    $stmt->product_id = $data['product'][$i];                    
                    $stmt->store_id = $data['store'][$i];
                    if ($data['purchase_type'] == 0 or $data['purchase_type'] == 1) {
                        $stmt->deb_qantity = $data['qantities'][$i];
                    } elseif ($data['purchase_type'] == 2) {
                        $stmt->cred_qantity = $data['qantities'][$i];
                    }
                    $stmt->price = $data['prices'][$i];
                    $stmt->user_id = $user_id;
                    $stmt->action_id = $data['purchase_type'];
                    $stmt->save();
                }
                if ($stmt = true) {
                    if ($data['payment_method'] != null and $data['pay'] != null) {
                        $voucer = new Voucer();
                        $voucer->bank_id = $data['payment_method'];
                        $voucer->dates = $data['date'];
                        $voucer->nickname = 'supplier';
                        $voucer->data_id = $data['supplier'];
                        if ($data['purchase_type'] == 2) {
                            $voucer->debit = $data['pay'];
                        } else {
                            $voucer->credit = $data['pay'];
                        }
                        $voucer->user_id = Auth::user()->id;
                        $voucer->save();
                        $v_id = $voucer->id;
                        $inv = Invpurchase::where('id', $inv_id)->update(['payment_id' => $v_id]);
                        return ['message' => 'Purchase Invoice and Payment Added Success', 'id' => $inv_id];
                    }
                    return ['message' => 'Purchase Invoice Added Success','id'=>$inv_id];
                }
            }
        }
        return response()->json([$validator->getMessageBag()]);
    }
    public function AllPurchase() {
        if (request()->ajax()) {
            $total_bal = 500;
            $get = DB::table('invpurchases')
                ->join('suppliers', 'suppliers.id', '=', 'invpurchases.supplier_id')
                ->select('invpurchases.id', 'invpurchases.dates', 'suppliers.name', 'invpurchases.action_id', 'invpurchases.total_payable', 'invpurchases.total')
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
                ->addColumn('type', function ($get) {
                    switch (intval($get->action_id)) {
                    case 0:
                        $type = "Purchase";
                        break;
                    case 1:
                        $type = "Advance";
                        break;
                    case 2:
                        $type = "Purchase Return";
                        break;
                    }
                    return $type;
                })
                ->addColumn('dates', function ($get) {
                    $date = date('d-m-Y', $get->dates);
                    return $date;
                })
                ->rawColumns(['action', 'type', 'dates'])->make(true);
        }
        return view('pages.purchase.all-purchase');
    }
    public function GetData($id){
        $invoice = DB::table('invpurchases')
            ->join('suppliers', 'suppliers.id', '=', 'invpurchases.supplier_id')
            ->leftJoin('voucers', 'invpurchases.payment_id', '=', 'voucers.id')
            ->leftJoin('banks', 'voucers.bank_id', '=', 'banks.id')
            ->leftJoin('transports', 'invpurchases.transport_id', '=', 'transports.id')
            ->selectRaw('invpurchases.id,invpurchases.transport_id,transports.name as t_name,transports.phone as t_phone,invpurchases.transport,invpurchases.dates,invpurchases.issue_date,invpurchases.supplier_id,suppliers.name,suppliers.phone,invpurchases.labour_cost,invpurchases.total_item,invpurchases.total_payable,invpurchases.action_id,invpurchases.total,invpurchases.dates,invpurchases.note,voucers.bank_id,banks.name bank_name,cast(ifnull(voucers.debit,0)+ifnull(voucers.credit,0) as decimal(20,2)) as ammount,voucers.id as payment_id')
            ->where('invpurchases.id', $id)
            ->first();
            // var_dump($invoice);
        $sales = DB::select("select purchases.id,purchases.product_id,purchases.invoice_id,products.product_name,purchases.deb_qantity+purchases.cred_qantity as qantity,purchases.price from purchases 
          inner join products on products.id=purchases.product_id
         where purchases.invoice_id=:id order by purchases.id asc", ['id' => $id]);
        $balance=$this->getBalance(intval($invoice->supplier_id))[0]->total;
        return response()->json(['invoice'=>$invoice,'sales'=>$sales,'balance'=>$balance]);
    }
    public function UpdateForm($id) {
            $invoice = DB::table('invpurchases')
            ->join('suppliers', 'suppliers.id', '=', 'invpurchases.supplier_id')
            ->leftJoin('voucers', 'invpurchases.payment_id', '=', 'voucers.id')
            ->leftJoin('banks', 'voucers.bank_id', '=', 'banks.id')
            ->leftJoin('transports', 'invpurchases.transport_id', '=', 'transports.id')
            ->selectRaw('invpurchases.id,invpurchases.transport_id,transports.name as t_name,transports.phone as t_phone,invpurchases.transport,invpurchases.dates,invpurchases.issue_date,invpurchases.supplier_id,suppliers.name,suppliers.phone,invpurchases.labour_cost,invpurchases.total_item,invpurchases.total_payable,invpurchases.action_id,invpurchases.total,invpurchases.dates,invpurchases.note,voucers.bank_id,banks.name bank_name,cast(ifnull(voucers.debit,0)+ifnull(voucers.credit,0) as decimal(20,2)) as ammount,voucers.id as payment_id')
            ->where('invpurchases.id', $id)
            ->first();
            // var_dump($invoice);
        if (isset($invoice->action_id) and ($invoice->action_id==3 or is_null($invoice))) {
            return abort(404);
        }
        $sales = DB::select("SELECT purchases.id,stores.id as store_id,stores.name as store_name,purchases.product_id,purchases.invoice_id,products.product_name,purchases.deb_qantity+purchases.cred_qantity as qantity,purchases.price from purchases 
          inner join products on products.id=purchases.product_id
          inner join stores on stores.id=purchases.store_id
         where purchases.invoice_id=:id order by purchases.id asc", ['id' => $id]);
        foreach($sales as $sale){
            $avlqty[]=$this->AvlQty($sale->store_id,$sale->product_id);
        }
        $avlqty=json_encode($avlqty);
        $invoice = json_encode($invoice);
        $sales = json_encode($sales);
        return view('pages.purchase.purchase-update', compact('invoice', 'sales','avlqty'));
    }
    public function Update(Request $r,$id){
        // return $r->all();
        $data['sale_id'] = array_combine(range(1, count(explode(',', $r->sale_id[0]))), explode(',', $r->sale_id[0]));
        $data['product'] = array_combine(range(1, count(explode(',', $r->product[0]))), explode(',', $r->product[0]));
        $data['store'] = array_combine(range(1, count(explode(',', $r->store[0]))), explode(',', $r->store[0]));
        $data['qantities'] = array_combine(range(1, count(explode(',', $r->qantities[0]))), explode(',', $r->qantities[0]));
        $data['prices'] = array_combine(range(1, count(explode(',', $r->prices[0]))), explode(',', $r->prices[0]));
        $data['supplier'] = $r->supplier;
        $data['date'] = $r->date;
        $data['issue_date'] = $r->issue_date;
        $data['total_payable'] = $r->total_payable;
        $data['total_item'] = $r->total_item;
        if (isset($r->labour)) {
            $data['labour'] = $r->labour;
        } else {
            $data['labour'] = null;
        }
        if ($r->transport_id != "null") {
            $data['transport_id'] = $r->transport_id;
        } else {
            $data['transport_id'] = null;
        }
        if (isset($r->transport)) {
            $data['transport'] = $r->transport_cost;
        }else{
            $data['transport']=null;
        }
        $data['action_id'] = $r->purchase_type;
        $data['transaction'] = $r->transaction;
        $data['payment_method'] = $r->payment_method;
        if ($r->payment_method == 'null') {
            $data['payment_method'] = null;
        }
        $data['pay'] = $r->pay;
        $data['payment_id'] = $r->payment_id;
        $data['total'] = $r->total;
        $data['note'] = $r->note;
        $validator = Validator::make($data, [
            'sale_id' => 'required|array',
            'sale_id.*' => 'required|distinct|regex:/^([0-9]+)$/',
            'product' => 'required|array',
            'product.*' => 'required|distinct|regex:/^([0-9]+)$/',
            'store' => 'required|array',
            'store.*' => 'required|regex:/^([0-9]+)$/',
            'qantities' => 'required|array',
            'qantities.*' => 'required|regex:/^([0-9.]+)$/',
            'prices' => 'required|array',
            'prices.*' => 'required|regex:/^([0-9.]+)$/',
            'discounts' => 'nullable|array',
            'discounts.*' => 'nullable|regex:/^([0-9.]+)$/',
            'transport' => 'nullable|regex:/^([0-9]+)$/',
            'transport_id' => 'nullable|regex:/^([0-9]+)$/',
            'action_id' => 'required|regex:/^([0-2]+)$/',
            'supplier' => 'required|regex:/^([0-9]+)$/',
            'date' => 'required|max:10|date_format:d-m-Y',
            'issue_date' => 'required|max:10|date_format:d-m-Y',
            'total_payable' => 'required|max:10|regex:/^([0-9.]+)$/',
            'total_item' => 'required|max:10|regex:/^([0-9.]+)$/',
            'labour' => 'nullable|max:15|regex:/^([0-9.]+)$/',
            'total' => 'required|max:15|regex:/^([0-9.]+)$/',
            'payment_method' => 'nullable|max:10|regex:/^([0-9]+)$/',
            'transaction' => 'nullable|max:30|regex:/^([a-zA-Z0-9]+)$/',
            'pay' => 'nullable|max:18|regex:/^([0-9.]+)$/',
            'note' => 'nullable|max:500',
        ]);
        if ($validator->passes()) {
            $invoice = Invpurchase::find($id);
            $invoice->dates = strtotime(strval($r->date));
            $invoice->issue_date = strtotime(strval($r->issue_date));
            $invoice->supplier_id = $data['supplier'];
            $invoice->total_item = $data['total_item'];
            $invoice->labour_cost = $data['labour'];
            $invoice->transport_id= $data['transport_id'];
            $invoice->transport= $data['transport'];
            $invoice->total_payable = $data['total_payable'];
            $invoice->total = $data['total'];
            $invoice->action_id = $data['action_id'];
            $invoice->user_id = Auth::user()->id;
            $invoice->save();
            $inv_id = $invoice->id;
            $user_id = $invoice->user_id;
            if ($invoice = true) {
                $length = intval($data['total_item']) - 1;
                for ($i = 0; $i <= $length; $i++) {
                    if ($data['sale_id'][$i + 1] != 0) {
                        $stmt = Purchase::find($data['sale_id'][$i + 1]);
                        $stmt->invoice_id = $inv_id;
                        $stmt->dates = strtotime(strval($r->date));
                        $stmt->supplier_id = $data['supplier'];
                        $stmt->product_id = $data['product'][$i + 1];
                        $stmt->store_id = $data['store'][$i + 1];

                        if ($data['action_id'] != 2) {
                         $stmt->deb_qantity = $data['qantities'][$i + 1];
                        } else {
                         $stmt->cred_qantity = $data['qantities'][$i + 1];
                        }
                        $stmt->price = $data['prices'][$i + 1];
                        $stmt->action_id = $data['action_id'];
                        $stmt->user_id = $user_id;
                        $stmt->save();
                    } else {
                        $stmt = new Purchase();
                        $stmt->invoice_id = $inv_id;
                        $stmt->dates = strtotime(strval($r->date));
                        $stmt->supplier_id = $data['supplier'];
                        $stmt->product_id = $data['product'][$i + 1];
                        $stmt->store_id = $data['store'][$i + 1];

                        if ($data['action_id'] != 2) {
                         $stmt->deb_qantity = $data['qantities'][$i + 1];
                        } else {
                         $stmt->cred_qantity = $data['qantities'][$i + 1];
                        }
                        $stmt->price = $data['prices'][$i + 1];
                        $stmt->action_id = $data['action_id'];
                        $stmt->user_id = $user_id;
                        $stmt->save();
                    }
                }
                if ($stmt = true) {
                    if ($data['payment_method'] != null and $data['pay'] != null and floatval($data['pay'])>0) {
                        $voucer = (Voucer::find($data['payment_id'])=='' ? new Voucer : Voucer::find($data['payment_id']));
                        $voucer->bank_id = $data['payment_method'];
                        $voucer->dates = strtotime(strval($r->date));
                        $voucer->nickname = 'supplier';
                        $voucer->data_id = $data['supplier'];
                        if ($data['action_id'] != 2) {
                            $voucer->debit = $data['pay'];
                            $voucer->credit = 0;
                        } else {
                            $voucer->credit = $data['pay'];
                            $voucer->debit = 0;
                        }
                        $voucer->user_id = Auth::user()->id;
                        $voucer->save();
                        $v_id = $voucer->id;
                        $inv = Invpurchase::where('id', $inv_id)->update(['payment_id' => $v_id]);
                        return response()->json(['message' => 'Invoice and Payment updated Success', 'id' => $inv_id,'balance'=>$this->getBalance(intval($data['supplier']))[0]->total]);
                    }else{
                        if(Voucer::find($data['payment_id'])!=''){
                           Voucer::find($data['payment_id'])->delete();
                        }
                    }
                    if ($inv = true) {
                        return ['message' => 'Purchases Updated', 'id' => $inv_id,'balance'=>$this->getBalance(intval($data['supplier']))[0]->total];
                    }
                }
            }
        }
        return response()->json([$validator->getMessageBag()]);
    }
    public function Delete($id){
        $data=Invpurchase::find($id);
        $payment_id=$data['payment_id'];
        $data->delete();
        if ($payment_id!=null) {
            $voucer=Voucer::find($payment_id)->delete();
        }
        if ($data){
            $notification = new Notification;
            $notification->details = 'Purchase No <strong>' . $id . '</strong>' . ' deleted by <strong>' . Auth::user()->name . '(' . Auth::user()->id . ')</strong>';
            $notification->action = 'delete';
            $save = $notification->save();
            if ($save) {
                return response()->json(['message' => 'Purchase Deleted Success']);
            }
        }
    }
    public function AvlQty($store_id,$product_id){
        $data=DB::select("
    SELECT ifnull((SELECT sum(deb_qantity)-sum(cred_qantity) from purchases where product_id=:product_id and store_id=:store_id),0)-ifnull((SELECT sum(deb_qantity)-sum(cred_qantity) from sales where product_id=:product_id and store_id=:store_id and (action_id=0 or action_id=2 or action_id=3)),0) as total
        ",['product_id'=>$product_id,'store_id'=>$store_id]);
      return $data[0]->total;
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
    return $blnce;
    }
}
