<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Validator;
use App\OrderInvoice;
use App\OrderSale;
use App\Voucer;
use Auth;
class OrderController extends Controller
{
    public function __construct(){
    	$this->middleware('auth');
    }
    public function Index(){
    	$sms=DB::table('information')->select('sms_api','sms_sender')->first();
    	return view("pages.order.order",compact('sms'));
    }
    public function Create(Request $r){
    	$data['product'] = array_combine(range(1, count(explode(',', $r->product[0]))), explode(',', $r->product[0]));
        $data['qantities'] = array_combine(range(1, count(explode(',', $r->qantities[0]))), explode(',', $r->qantities[0]));
        $data['prices'] = array_combine(range(1, count(explode(',', $r->prices[0]))), explode(',', $r->prices[0]));
        $data['bundle'] = array_combine(range(1, count(explode(',', $r->bundle[0]))), explode(',', $r->bundle[0]));
        $data['customer'] = $r->customer;
        $data['date'] = $r->date;
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
        if (isset($r->transport_cost)) {
            $data['transport_cost'] = $r->transport_cost;
        }else{
            $data['transport_cost']=null;
        }

        $data['sales_type'] = $r->sales_type;
        if ($r->site!='' && $r->site!=="null") {
            $data['site_id'] = $r->site;
        }else{
            $data['site_id'] = null;
        }
        if ($r->delivery!='' && $r->delivery!=="null") {
            $data['delivery_id'] = $r->delivery;
        }else{
            $data['delivery_id'] = null;
        }
        $data['transaction'] = $r->transaction;
        $data['payment_method'] = $r->payment_method;
        if ($r->payment_method == 'null') {
            $data['payment_method'] = null;
        }
        $data['pay'] = $r->pay;
        $data['total'] = $r->total;
        $data['note'] = $r->note;
        $validator = Validator::make($data, [
            'product'        => 'required|array',
            'product.*'      => 'required|distinct|regex:/^([0-9]+)$/',
            'qantities'      => 'required|array',
            'qantities.*'    => 'required|regex:/^([0-9.]+)$/',
            'prices'         => 'required|array',
            'prices.*'       => 'required|regex:/^([0-9.]+)$/',
            'bundle'         => 'required|array',
            'bundle.*'       => 'nullable|regex:/^([0-9]+)$/',
            'transport'      => 'nullable|regex:/^([0-9]+)$/',
            'sales_type'     => 'required|regex:/^([0-3]+)$/',            
            'site_id'        => 'nullable|regex:/^([0-9]+)$/',            
            'delivery_id'    => 'required|regex:/^([0-9]+)$/',
            'customer'       => 'required|regex:/^([0-9]+)$/',
            'date'           => 'required|max:10|date_format:d-m-Y',
            'total_payable'  => 'required|max:10|regex:/^([0-9.]+)$/',
            'total_item'     => 'required|max:10|regex:/^([0-9.]+)$/',
            'discount'       => 'nullable|max:15|regex:/^([0-9.]+)$/',
            'vat'            => 'nullable|max:15|regex:/^([0-9.]+)$/',
            'labour'         => 'nullable|max:15|regex:/^([0-9.]+)$/',
            'total'          => 'required|max:15|regex:/^([0-9.]+)$/',
            'payment_method' => 'nullable|max:10|regex:/^([0-9]+)$/',
            'transaction'    => 'nullable|max:30|regex:/^([a-zA-Z0-9]+)$/',
            'pay'            => 'nullable|max:18|regex:/^([0-9.]+)$/',
            'note'           => 'nullable|max:500',
        ]);
        if ($validator->passes()) {
            $invoice = new OrderInvoice;
            $invoice->dates = strtotime(strval($data['date']));
            $invoice->customer_id = $data['customer'];        
            $invoice->site_id = $data['site_id'];
            $invoice->total_item = $data['total_item'];
            $invoice->discount = $data['discount'];
            $invoice->vat = $data['vat'];
            $invoice->labour_cost = $data['labour'];
            $invoice->transport = $data['transport_cost'];
            $invoice->total_payable = $data['total_payable'];
            $invoice->total = $data['total'];
            $invoice->action_id = 3;
            $invoice->note = $data['note'];
            $invoice->user_id = Auth::user()->id;            
            $invoice->delivery_id = $data['delivery_id'];
            $invoice->save();
            $inv_id = $invoice->id;
            $user_id = $invoice->user_id;
            if ($invoice = true) {
                $length = intval($data['total_item']) - 1;
                for ($i = 0; $i <= $length; $i++) {
                    $stmt = new OrderSale();
                    $stmt->invoice_id = $inv_id;
                    $stmt->dates = strtotime(strval($data['date']));
                    $stmt->customer_id = $data['customer'];
                    $stmt->product_id = $data['product'][$i + 1];
                    $stmt->bundle = $data['bundle'][$i + 1];
                    if ($data['sales_type'] != 2) {
                        $stmt->deb_qantity = $data['qantities'][$i + 1];
                    } else {
                        $stmt->cred_qantity = $data['qantities'][$i + 1];
                    }
                    $stmt->price = $data['prices'][$i + 1];
                    $stmt->user_id = $user_id;
                    $stmt->action_id = 3;
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
                        $voucer->order_id = $inv_id;
                        $voucer->user_id = Auth::user()->id;
                        $voucer->save();
                        $v_id = $voucer->id;
                        $inv = OrderInvoice::where('id', $inv_id)->update(['payment_id' => $v_id]);
                        return ['message' => 'Order and Payment Added Success', 'id' => $inv_id];
                    }
                    return ['message' => 'Order Added Success', 'id' => $inv_id];
                }
            }
        }
        return response()->json([$validator->getMessageBag()]);
    }
}
