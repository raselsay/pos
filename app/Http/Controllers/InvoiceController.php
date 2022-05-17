<?php

namespace App\Http\Controllers;

use App\Invoice;
use App\Notification;
use App\Sale;
use App\Voucer;
use Auth;
use DataTables;
use DB;
use Illuminate\Http\Request;
use URL;
use Validator;

class InvoiceController extends Controller {
    public function __construct() {
        $this->middleware('auth');
    }
    public function invoiceForm() {
        $sms=DB::table('information')->select('sms_api','sms_sender')->first();
        return view('pages.invoice.invoice',compact('sms'));
    }
    public function insertInvoice(Request $r) {
        $data['product'] = array_combine(range(1, count(explode(',', $r->product[0]))), explode(',', $r->product[0]));
        $data['store'] = array_combine(range(1, count(explode(',', $r->store[0]))), explode(',', $r->store[0]));
        $data['qantities'] = array_combine(range(1, count(explode(',', $r->qantities[0]))), explode(',', $r->qantities[0]));
        $data['prices'] = array_combine(range(1, count(explode(',', $r->prices[0]))), explode(',', $r->prices[0]));
        $data['bundle'] = array_combine(range(1, count(explode(',', $r->bundle[0]))), explode(',', $r->bundle[0]));
        $data['customer'] = $r->customer;
        $data['date'] = $r->date;
        $data['issue_date'] = $r->issue_date;
        $data['total_payable'] = $r->total_payable;
        $data['total_item'] = $r->total_item;
        if (isset($r->discount)) {
            $data['discount'] = $r->discount;
        } else {
            $data['discount'] = null;
        }
        if (isset($r->vat)) {
            $data['vat'] = $r->vat;
        } else {
            $data['vat'] = null;
        }
        if (isset($r->labour)) {
            $data['labour'] = $r->labour;
        } else {
            $data['labour'] = null;
        }
        if (isset($r->transport) and $r->transport != "null") {
            $data['transport'] = $r->transport;
        } else {
            $data['transport'] = null;
        }
        if (isset($r->transport_cost)) {
            $data['transport_cost'] = $r->transport_cost;
        }else{
            $data['transport_cost']=null;
        }
        if (isset($r->site) and $r->site!='null') {
            $data['site'] = $r->site;
        }else{
            $data['site']=null;
        }
        $data['sales_type'] = $r->sales_type;
        $data['transaction'] = $r->transaction;
        $data['payment_method'] = $r->payment_method;
        if ($r->payment_method == 'null') {
            $data['payment_method'] = null;
        }
        $data['pay'] = $r->pay;
        $data['total'] = $r->total;
        $data['note'] = $r->note;
        $validator = Validator::make($data, [
            'product' => 'required|array',
            'product.*' => 'required|distinct|regex:/^([0-9]+)$/',
            'store' => 'required|array',
            'store.*' => 'required|regex:/^([0-9]+)$/',
            'qantities' => 'required|array',
            'qantities.*' => 'required|regex:/^([0-9.]+)$/',
            'prices' => 'required|array',
            'prices.*' => 'required|regex:/^([0-9.]+)$/',
            'bundle' => 'required|array',
            'bundle.*' => 'nullable|max:200',
            'transport' => 'nullable|regex:/^([0-9]+)$/',
            'site' => 'nullable|regex:/^([0-9]+)$/',
            'transport_cost' => 'nullable|regex:/^([0-9.]+)$/',
            'sales_type' => 'required|regex:/^([0-2]+)$/',
            'customer' => 'required|regex:/^([0-9]+)$/',
            'date' => 'required|max:10|date_format:d-m-Y',
            'issue_date' => 'required|max:10|date_format:d-m-Y',
            'total_payable' => 'required|max:10|regex:/^([0-9.]+)$/',
            'total_item' => 'required|max:10|regex:/^([0-9.]+)$/',
            'discount' => 'nullable|max:15|regex:/^([0-9.]+)$/',
            'vat' => 'nullable|max:15|regex:/^([0-9.]+)$/',
            'labour' => 'nullable|max:15|regex:/^([0-9.]+)$/',
            'total' => 'required|max:15|regex:/^([0-9.]+)$/',
            'payment_method' => 'nullable|max:10|regex:/^([0-9]+)$/',
            'transaction' => 'nullable|max:30|regex:/^([a-zA-Z0-9]+)$/',
            'pay' => 'nullable|max:18|regex:/^([0-9.]+)$/',
            'note' => 'nullable|max:500',
        ]);
        if ($validator->passes()) {
            $invoice = new Invoice;
            $invoice->dates = strtotime(strval($data['date']));
            if ($data['sales_type'] == 1) {
                $invoice->issue_dates = strtotime(strval($data['issue_date']));
            }
            $invoice->customer_id = $data['customer'];
            $invoice->total_item = $data['total_item'];
            $invoice->discount = $data['discount'];
            $invoice->vat = $data['vat'];
            $invoice->labour_cost = $data['labour'];
            $invoice->transport = $data['transport_cost'];
            if (isset($data['transport'])) {
                $invoice->transport_id = $data['transport'];
            }
            $invoice->site_id = $data['site'];
            $invoice->total_payable = $data['total_payable'];
            $invoice->total = $data['total'];
            $invoice->action_id = $data['sales_type'];
            $invoice->note = $data['note'];
            $invoice->user_id = Auth::user()->id;
            $invoice->save();
            $inv_id = $invoice->id;
            $user_id = $invoice->user_id;
            if ($invoice = true) {
                $length = intval($data['total_item']) - 1;
                for ($i = 0; $i <= $length; $i++) {
                    $stmt = new Sale();
                    $stmt->invoice_id = $inv_id;
                    $stmt->dates = strtotime(strval($data['date']));
                    $stmt->customer_id = $data['customer'];
                    $stmt->product_id = $data['product'][$i + 1];
                    $stmt->store_id = $data['store'][$i + 1];
                    $stmt->bundle = $data['bundle'][$i + 1];
                    if ($data['sales_type'] != 2) {
                        $stmt->deb_qantity = $data['qantities'][$i + 1];
                    } else {
                        $stmt->cred_qantity = $data['qantities'][$i + 1];
                    }
                    $stmt->price = $data['prices'][$i + 1];
                    $stmt->user_id = $user_id;
                    $stmt->action_id = $data['sales_type'];
                    $stmt->save();
                }
                if ($stmt = true) {
                    if ($data['payment_method'] != null and $data['pay'] != null) {
                        $voucer = new Voucer();
                        $voucer->bank_id = $data['payment_method'];
                        $voucer->dates = strtotime(strval($data['date']));
                        $voucer->nickname = 'customer';
                        $voucer->data_id = $data['customer'];
                        if ($data['sales_type'] != 2) {
                            $voucer->debit = $data['pay'];
                        } else {
                            $voucer->credit = $data['pay'];
                        }

                        $voucer->invoice_id = $inv_id;
                        $voucer->user_id = Auth::user()->id;
                        $voucer->save();
                        $v_id = $voucer->id;
                        $inv = Invoice::where('id', $inv_id)->update(['payment_id' => $v_id]);
                        return ['message' => 'Invoice and Payment Added Success', 'id' => $inv_id];
                    }
                    return ['message' => 'Invoice Added Success', 'id' => $inv_id];
                }
            }
        }
        return response()->json([$validator->getMessageBag()]);
    }

    public function getChildCat($id = null) {
        $data = DB::table('child_categories')->select('id', 'name')->where('cat_id', $id)->get();
        return [$data];
    }
    public function allInvoices() {
        if (request()->ajax()) {
            $get = DB::table('invoices')
                ->join('customers', 'customers.id', '=', 'invoices.customer_id')
                ->select('invoices.id', 'invoices.dates', 'customers.name','customers.adress', 'invoices.action_id', 'invoices.total_payable', 'invoices.total')
                ->where('invoices.action_id', 0)
                ->orWhere('invoices.action_id', 1)
                ->orWhere('invoices.action_id', 2)
                ->orderByRaw('invoices.id desc')
                ->get();
            return DataTables::of($get)
                ->addIndexColumn()
                ->addColumn('action', function ($get) {
                    $button = '<div class="btn-group btn-group-toggle" data-toggle="buttons">
                                <a type="button" href="' . URL::to('admin/invoice-update') . '/' . $get->id . '" class="btn btn-sm btn-primary rounded mr-1 edit"><i class="fas fa-eye"></i></a>
                                <a class="btn btn-danger btn-sm rounded delete mr-1" data-id="' . $get->id . '"><i class="fas fa-trash-alt"></i></a>
                                <a class="btn btn-secondary btn-sm rounded print" data-id="' . $get->id . '"><i class="fas fa-print"></i></a>
                               </div>';
                    return $button;
                })
                ->addColumn('type', function ($get) {
                    switch (intval($get->action_id)) {
                    case 0:
                        $type = "Sale";
                        break;
                    case 1:
                        $type = "Advance";
                        break;
                    case 2:
                        $type = "Sale Return";
                        break;
                    }
                    return $type;
                })
                ->addColumn('name', function ($get) {
                    $customer=$get->name.(($get->adress!=null) ? '('.$get->adress.')' : '');
                    return $customer;
                })
                ->addColumn('dates', function ($get) {
                    $date = date('d-m-Y', $get->dates);
                    return $date;
                })
                ->addColumn('products', function ($get) {
                    $product_name='';                    
                    $store_name='';
                    $qantity='';

                    $sales = DB::select("
                    SELECT products.product_name,stores.name,(sales.deb_qantity-sales.cred_qantity) qantity FROM sales
                    inner join stores on stores.id=sales.store_id 
                    inner join products on products.id=sales.product_id
                    where sales.invoice_id=:id
                        ",['id'=>$get->id]);
                    foreach($sales as $sale){
                        $product_name.=$sale->product_name;
                        $store_name.='('.$sale->qantity.')';
                        $qantity.='('.$sale->name.')';
                    }
                    return $product_name.$store_name.$qantity;
                })
                ->rawColumns(['action', 'type', 'dates','products','name'])->make(true);
        }
        return view('pages.invoice.all_invoices');
    }
    public function UpdateForm($id) {
            $invoice = DB::table('invoices')
            ->join('customers', 'customers.id', '=', 'invoices.customer_id')
            ->leftJoin('voucers', 'invoices.payment_id', '=', 'voucers.id')
            ->leftJoin('banks', 'voucers.bank_id', '=', 'banks.id')
            ->leftJoin('transports', 'invoices.transport_id', '=', 'transports.id')
            ->leftJoin('sites', 'invoices.site_id', '=', 'sites.id')
            ->selectRaw('invoices.id,invoices.transport_id,transports.name as t_name,transports.phone as t_phone,invoices.transport,invoices.dates,invoices.issue_dates,invoices.customer_id,customers.name,customers.phone1,sites.id as site_id,sites.name as site_name,invoices.discount,invoices.vat,invoices.labour_cost,invoices.total_item,invoices.total_payable,invoices.action_id,invoices.total,invoices.dates,invoices.note,voucers.bank_id,banks.name bank_name,cast(ifnull(voucers.debit,0)+ifnull(voucers.credit,0) as decimal(20,2)) as ammount,voucers.id as payment_id')
            ->where('invoices.id', $id)
            ->first();
            // var_dump($invoice);
        if (isset($invoice->action_id) and ($invoice->action_id==3 or is_null($invoice))) {
            return abort(404);
        }
        $sales = DB::select("SELECT sales.id,stores.id as store_id,stores.name as store_name,sales.product_id,sales.invoice_id,products.product_name,sales.deb_qantity+sales.cred_qantity as qantity,sales.price,sales.discount,sales.bundle from sales 
          inner join products on products.id=sales.product_id
          inner join stores on stores.id=sales.store_id
         where sales.invoice_id=:id order by sales.id asc", ['id' => $id]);
        foreach($sales as $sale){
            $avlqty[]=$this->AvlQty($sale->store_id,$sale->product_id);
        }
        $avlqty=json_encode($avlqty);
        $invoice = json_encode($invoice);
        $sales = json_encode($sales);
        return view('pages.invoice.invoice-update', compact('invoice', 'sales','avlqty'));
    }
    public function Update(Request $r, $id) {
        // return $r->all();
        $data['sale_id'] = array_combine(range(1, count(explode(',', $r->sale_id[0]))), explode(',', $r->sale_id[0]));
        $data['product'] = array_combine(range(1, count(explode(',', $r->product[0]))), explode(',', $r->product[0]));
        $data['store'] = array_combine(range(1, count(explode(',', $r->store[0]))), explode(',', $r->store[0]));
        $data['qantities'] = array_combine(range(1, count(explode(',', $r->qantities[0]))), explode(',', $r->qantities[0]));
        $data['prices'] = array_combine(range(1, count(explode(',', $r->prices[0]))), explode(',', $r->prices[0]));
        $data['bundle'] = array_combine(range(1, count(explode(',', $r->bundle[0]))), explode(',', $r->bundle[0]));
        $data['customer'] = $r->customer;
        $data['date'] = $r->date;
        $data['issue_date'] = $r->issue_date;
        $data['total_payable'] = $r->total_payable;
        $data['total_item'] = $r->total_item;
        if (isset($r->discount)) {
            $data['discount'] = $r->discount;
        } else {
            $data['discount'] = null;
        }
        if (isset($r->vat)) {
            $data['vat'] = $r->vat;
        } else {
            $data['vat'] = null;
        }
        if (isset($r->labour)) {
            $data['labour'] = $r->labour;
        } else {
            $data['labour'] = null;
        }
        if ($r->transport != "null") {
            $data['transport'] = $r->transport;
        } else {
            $data['transport'] = null;
        }
        if (isset($r->transport_cost)) {
            $data['transport_cost'] = $r->transport_cost;
        }else{
            $data['transport_cost']=null;
        }
        if (isset($r->site) and $r->site!='null') {
            $data['site'] = $r->site;
        }else{
            $data['site']=null;
        }
        $data['action_id'] = $r->sales_type;
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
            'bundle' => 'nullable|array',
            'bundle.*' => 'nullable|max:200',
            'transport' => 'nullable|regex:/^([0-9]+)$/',
            'action_id' => 'required|regex:/^([0-2]+)$/',
            'customer' => 'required|regex:/^([0-9]+)$/',
            'date' => 'required|max:10|date_format:d-m-Y',
            'issue_date' => 'required|max:10|date_format:d-m-Y',
            'total_payable' => 'required|max:10|regex:/^([0-9.]+)$/',
            'total_item' => 'required|max:10|regex:/^([0-9.]+)$/',
            'discount' => 'nullable|max:15|regex:/^([0-9.]+)$/',
            'vat' => 'nullable|max:15|regex:/^([0-9.]+)$/',
            'labour' => 'nullable|max:15|regex:/^([0-9.]+)$/',
            'total' => 'required|max:15|regex:/^([0-9.]+)$/',
            'payment_method' => 'nullable|max:10|regex:/^([0-9]+)$/',
            'transaction' => 'nullable|max:30|regex:/^([a-zA-Z0-9]+)$/',
            'pay' => 'nullable|max:18|regex:/^([0-9.]+)$/',
            'note' => 'nullable|max:500',
        ]);
        if ($validator->passes()) {
            $invoice = Invoice::find($id);
            $invoice->dates = strtotime(strval($data['date']));
            $invoice->issue_dates = strtotime(strval($data['issue_date']));
            $invoice->customer_id = $data['customer'];
            $invoice->total_item = $data['total_item'];
            $invoice->discount = $data['discount'];
            $invoice->vat = $data['vat'];
            $invoice->labour_cost = $data['labour'];
            $invoice->transport= $data['transport_cost'];
            $invoice->total_payable = $data['total_payable'];
            $invoice->total = $data['total'];
            $invoice->site_id=$data['site'];
            $invoice->action_id = $data['action_id'];
            $invoice->note = $data['note'];
            $invoice->user_id = Auth::user()->id;
            $invoice->save();
            $inv_id = $invoice->id;
            $user_id = $invoice->user_id;
            if ($invoice = true) {
                $length = intval($data['total_item']) - 1;
                for ($i = 0; $i <= $length; $i++) {
                    if ($data['sale_id'][$i + 1] != 0) {
                        $stmt = Sale::find($data['sale_id'][$i + 1]);
                        $stmt->invoice_id = $inv_id;
                        $stmt->dates = strtotime(strval($data['date']));
                        $stmt->customer_id = $r->customer;
                        $stmt->product_id = $data['product'][$i + 1];                        
                        $stmt->store_id = $data['store'][$i + 1];

                        if ($data['action_id'] != 2) {
                         $stmt->deb_qantity = $data['qantities'][$i + 1];
                        } else {
                         $stmt->cred_qantity = $data['qantities'][$i + 1];
                        }
                        $stmt->bundle = $data['bundle'][$i + 1];
                        $stmt->price = $data['prices'][$i + 1];
                        $stmt->action_id = $data['action_id'];
                        $stmt->user_id = $user_id;
                        $stmt->save();
                    } else {
                        $stmt = new Sale();
                        $stmt->invoice_id = $inv_id;
                        $stmt->dates = strtotime(strval($data['date']));
                        $stmt->customer_id = $r->customer;
                        $stmt->product_id = $data['product'][$i + 1]; 
                        $stmt->store_id = $data['store'][$i + 1];

                        if ($data['action_id'] != 2) {
                         $stmt->deb_qantity = $data['qantities'][$i + 1];
                        } else {
                         $stmt->cred_qantity = $data['qantities'][$i + 1];
                        }
                        $stmt->bundle = $data['bundle'][$i + 1];
                        $stmt->price = $data['prices'][$i + 1];
                        $stmt->action_id = $data['action_id'];
                        $stmt->user_id = $user_id;
                        $stmt->save();
                    }
                }
                if ($stmt = true) {
                    if ($data['payment_method'] != null and $data['pay'] != null and intval($data['pay'])>0) {
                        $voucer = (Voucer::find($data['payment_id'])=='' ? new Voucer : Voucer::find($data['payment_id']));
                        $voucer->bank_id = $data['payment_method'];
                        $voucer->dates = strtotime(strval($data['date']));
                        $voucer->nickname = 'customer';
                        $voucer->data_id = $data['customer'];
                        if ($data['action_id'] != 2) {
                            $voucer->debit = $data['pay'];
                            $voucer->credit = 0;
                        } else {
                            $voucer->credit = $data['pay'];
                            $voucer->debit = 0;
                        }
                        $voucer->invoice_id = $inv_id;
                        $voucer->user_id = Auth::user()->id;
                        $voucer->save();
                        $v_id = $voucer->id;
                        $inv = Invoice::where('id', $inv_id)->update(['payment_id' => $v_id]);
                        return ['message' => 'Invoice and Payment updated Success', 'id' => $inv_id,'balance'=>$this->getCustomerBalance(intval($r->customer))[0]->total];
                    }else{
                        if(Voucer::find($data['payment_id'])!=''){
                           Voucer::find($data['payment_id'])->delete();
                        }
                    }
                    if ($inv = true) {
                        return ['message' => 'Invoice Updated', 'id' => $inv_id,'balance'=>$this->getCustomerBalance(intval($r->customer))[0]->total];
                    }
                }
            }
        }
        return response()->json([$validator->getMessageBag()]);
    }
    public function Delete($id = null) {
        $delete = Invoice::where('id', $id)->delete();
        if ($delete) {
            $notification = new Notification;
            $notification->details = 'Invoice No <strong>' . $id . '</strong>' . ' deleted by <strong>' . Auth::user()->name . '(' . Auth::user()->id . ')</strong>';
            $notification->action = 'delete';
            $save = $notification->save();
            if ($save) {
                return response()->json(['message' => 'Invoice Deleted Success']);
            }
        }
    }

    public function GetInvoiceData($id) {
        $invoice = DB::table('invoices')
            ->join('customers', 'customers.id', '=', 'invoices.customer_id')
            ->leftJoin('voucers', 'invoices.payment_id', '=', 'voucers.id')
            ->leftJoin('sites', 'invoices.site_id', '=', 'sites.id')
            ->selectRaw('invoices.id,invoices.customer_id,customers.name,customers.phone1,sites.id as site_id,concat(sites.name,"(",sites.adress,")") as site_name,invoices.discount,invoices.vat,invoices.labour_cost,invoices.total_item,invoices.total_payable,invoices.total,invoices.dates,invoices.issue_dates,invoices.action_id,invoices.note,if(invoices.action_id=0,voucers.debit,voucers.credit) payment')
            ->where('invoices.id', $id)
            ->first();
        $sales = DB::select("select sales.id,sales.product_id,sales.bundle,sales.invoice_id,products.product_name,sales.deb_qantity+sales.cred_qantity as qantity,sales.price,sales.discount from sales inner join products on products.id=sales.product_id
         where sales.invoice_id=:id order by sales.id asc", ['id' => $id]);

        $balance = $this->getCustomerBalance($invoice->customer_id);
        $invoice = json_encode($invoice);
        // $sales = json_encode($sales);
        return response()->json([
            'invoice' => $invoice,
            'sales' => $sales,
            'balance' => isset($balance[0]->total) ? $balance[0]->total : '',
        ]);
    }
    public function AvlQty($store_id,$product_id){
        $data=DB::select("
    SELECT ifnull((SELECT sum(deb_qantity)-sum(cred_qantity) from purchases where product_id=:product_id and store_id=:store_id),0)-ifnull((SELECT sum(deb_qantity)-sum(cred_qantity) from sales where product_id=:product_id and store_id=:store_id and (action_id=0 or action_id=2 or action_id=3)),0) as total
        ",['product_id'=>$product_id,'store_id'=>$store_id]);
      return $data[0]->total;
    }
    public function getCustomerBalance($id) {
        if (!preg_match("/[^0-9]/", $id)) {
            $get = DB::select("
            SELECT
    cast(((t.Deposit+t.total_payablebacks)-(t.Expence+t.total_payable))+t.op_blnce as decimal(16,2)) as total
from(
    select
    ifnull(sum(ifnull(voucers.debit,0)),0) as Deposit,
    ifnull(sum(ifnull(voucers.credit,0)),0) as Expence,
    ifnull((select sum(total_payable) from invoices where customer_id=:id and (action_id=0 or action_id=3)),0) as total_payable,
    ifnull((select sum(total_payable) from invoices where customer_id=:id and action_id=2),0) as total_payablebacks,
    (select opening_balance from customers where id=:id) as op_blnce
    from voucers
    left join names on names.id=voucers.category  where (voucers.data_id=:id and voucers.nickname='customer') or (voucers.data_id=:id and names.table_name='customers')
    ) t", ['id' => $id]);
            return $get;
        } else {
            return ['data' => 'something wrong here'];
        }
    }
}
