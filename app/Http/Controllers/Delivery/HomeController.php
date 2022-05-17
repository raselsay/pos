<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use DataTables;
use App\OrderInvoice;
use App\OrderSale;
use App\Invoice;
use App\Sale;
use Auth;
use Validator;
class HomeController extends Controller
{
	public function __construct(){
		$this->middleware("auth:delivery");
	}
    public function index(){
    	return view("delivery.home");
    }
    public function AllOrder(){
    	if (request()->ajax()) {
            $get = DB::select("
           select order_invoices.id,customers.name,order_invoices.total_item from order_invoices
           inner join customers on customers.id=order_invoices.customer_id
           where order_invoices.action_id=3 and order_invoices.delivery_id=:delivery_id
            ",['delivery_id'=>Auth::user()->id]);
            return DataTables::of($get)
                ->addIndexColumn()
                ->addColumn('status',function ($get){
                    $sales=DB::select("
                        SELECT order_sales.product_id,order_sales.deb_qantity-ifnull(sum(sales.deb_qantity),0) as qty from order_sales 
                        left join sales on order_sales.product_id=sales.product_id and order_sales.invoice_id=sales.order_id
                        where order_sales.invoice_id=:id
                        group by sales.product_id
                    ",['id'=>$get->id]);
                    $val=0;
                    for ($i=0; $i <count($sales) ; $i++) {
                        $val+=$sales[$i]->qty;
                    }
                    if ($val==0) {
                        return "Delivered";
                    }else{
                        return "Pending";
                    }
                })
                ->addColumn('action', function ($get) {
                    $button = '<div class="btn-group btn-group-toggle" data-toggle="buttons">
                       <button type="button" data-id="' . $get->id . '" class="btn btn-sm btn-primary rounded mr-1 edit" data-toggle="modal" data-target="">See More</button>
                    </div>';
                    return $button;
                })
                ->rawColumns(['action'])->make(true);
        }
        return view('delivery.home');
    }
    public function OrderSales($id=null){
        $get = DB::select("
           SELECT order_sales.id,products.product_name,products.id as product_id,(order_sales.deb_qantity-ifnull(sum(sales.deb_qantity),0)) deb_qantity,order_sales.price from order_sales
           inner join products on products.id=order_sales.product_id
           left join sales on sales.order_id=order_sales.invoice_id and sales.product_id=order_sales.product_id
           where order_sales.invoice_id=:order_id
           group by order_sales.product_id
            ",['order_id'=>$id]);
        return response()->json($get);
    }
    public function ConfirmOrder($id,Request $r){
        $validator=Validator::make($r->all(),[
            'qantity' => 'required|array',
            'qantity.*' => 'required|regex:/^([0-9.]+)$/',
            'store' => 'required|array',
            'store.*' => 'required|regex:/^([0-9]+)$/',            
            'transport' => 'required|regex:/^([0-9]+)$/',
        ]);
        $orderinvoice=OrderInvoice::find($id);        
        $ordersale=OrderSale::where('invoice_id',$id)->get();
        if ($validator->passes()) {
            $invoice=new Invoice;     
            $invoice->dates = $orderinvoice->dates;
            $invoice->issue_dates = $orderinvoice->issue_dates;
            $invoice->customer_id = $orderinvoice->customer_id;            
            $invoice->site_id = $orderinvoice->site_id;
            $invoice->total_item = $orderinvoice->total_item;
            $invoice->discount = $orderinvoice->discount;
            $invoice->vat = $orderinvoice->vat;
            $invoice->labour_cost = $orderinvoice->labour_cost;
            $invoice->transport = $orderinvoice->transport;
            $invoice->transport_id = $r->transport;
            $invoice->total_payable = $orderinvoice->total_payable;       
            $invoice->payment_id = $orderinvoice->payment_id;
            $invoice->total = $orderinvoice->total;;
            $invoice->action_id = 0;
            $invoice->note = $orderinvoice->note;
            $invoice->user_id = Auth::user()->id;            
            $invoice->delivery_id = $orderinvoice->delivery_id;
            $invoice->order_id = $orderinvoice->id;
            $invoice->save();
            $inv_id = $invoice->id;
            $user_id = $invoice->user_id;
            if ($invoice) {
                for ($i=0; $i <$ordersale->count() ; $i++) { 
                    $stmt = new Sale();
                    $stmt->invoice_id   = $inv_id;
                    $stmt->dates        = $ordersale[$i]->dates;
                    $stmt->customer_id  = $ordersale[$i]->customer_id;
                    $stmt->product_id   = $ordersale[$i]->product_id;
                    $stmt->store_id     = $r->store[$i];
                    $stmt->bundle       = $ordersale[$i]->bundle;
                    $stmt->deb_qantity  = $r->qantity[$i];
                    $stmt->price        = $ordersale[$i]->price;
                    $stmt->user_id      = $user_id;
                    $stmt->order_id     = $orderinvoice->id;
                    $stmt->action_id    = 0;
                    $stmt->save();
                }
                return response()->json(['message'=>'Order Approved Success']);
            }
        }
        return response()->json($validator->getMessageBag());
    }
    public function SearchStoreByDelivery(Request $r){
        if (!preg_match("/[^a-zA-Z0-9. ]/", $r->searchTerm) ) {
                $data = DB::select("SELECT stores.id,stores.name from  dpermissions
                inner join stores on stores.id=dpermissions.store_id  
                 where stores.name like :key and dpermissions.delivery_id=:del_id limit 10",['key'=>'%'.$r->searchTerm.'%','del_id'=>Auth::user()->id]);
                foreach ($data as $value) {
                    $set_data[] = ['id' => $value->id, 'text' => $value->name];
                }
                if (isset($set_data)) {
                    return response()->json($set_data);
                } else {
                    return response()->json(['message' => 'not found']);
                }
            }
    }
    public function getQantity($product_id,$store_id){
      $data=DB::select("
    SELECT ifnull((SELECT sum(deb_qantity)-sum(cred_qantity) from purchases where product_id=:product_id and store_id=:store_id and (action_id=0 or action_id=2 or action_id=3 or action_id=4 or action_id=5)),0)-ifnull((SELECT sum(deb_qantity)-sum(cred_qantity) from sales where product_id=:product_id and store_id=:store_id and (action_id=0 or action_id=2 or action_id=3)),0) as total
        ",['product_id'=>$product_id,'store_id'=>$store_id]);
      return response()->json($data);
    }
    public function getTransportExport(Request $r){
            $data=DB::select("SELECT id,name,phone from transports where (name like :term or  phone like :term) and status=1
            and type='Export' limit 10",['term'=>'%'.$r->searchTerm.'%']);
            foreach ($data as $value) {
                $set_data[]=['id'=>$value->id,'text'=>$value->name.'('.$value->phone.')'];
            }
            if (isset($set_data)) {
                return $set_data;
            }
    }
}
