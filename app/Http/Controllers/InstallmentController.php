<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Invoice;
use App\Sale;
use App\Voucer;
use Auth;
class InstallmentController extends Controller
{
    public function __construct(){
    	$this->middleware('auth');
    }
    public function Form(){
    	return view('pages.installment.installment');
    }
    public function Create(Request $r){
    	$data['product']=array_combine(range(1,count(explode(',',$r->product[0]))),explode(',',$r->product[0]));
    	$data['qantities']=array_combine(range(1,count(explode(',',$r->qantities[0]))),explode(',',$r->qantities[0]));
    	$data['prices']=array_combine(range(1,count(explode(',',$r->prices[0]))),explode(',',$r->prices[0]));
		$data['discounts']=array_combine(range(1,count(explode(',',$r->discounts[0]))),explode(',',$r->discounts[0]));
    	$data['customer']=$r->customer;
    	$data['date']=$r->date;
    	$data['issue_date']=$r->issue_date;
    	$data['installment_type']=$r->installment_type;
    	$data['total_installment']=$r->total_installment;
        $data['minimum_pay_percent']=$r->minimum_pay_percent;
    	$data['interest']=$r->interest;
    	$data['total_payable']=$r->total_payable;
    	$data['total_item']=$r->total_item;
        if (isset($discount)) {
            $data['discount']=$r->discount;
        }else{
            $data['discount']=null;
        }
        if (isset($r->vat)) {
            $data['vat']=$r->vat;
        }else{
            $data['vat']=null;
        }
        if (isset($r->labour)) {
            $data['labour']=$r->labour;
        }else{
            $data['labour']=null;
        }
		if($r->transport!='null'){
			$data['transport']=$r->transport;
		}else{
            $data['transport']=null;
        }
        if (isset($r->transport_cost)) {
            $data['transport_cost']=$r->transport_cost;
        }else{
            $data['transport_cost']=null;
        }
    	$data['sales_type']=$r->sales_type;
        $data['transaction']=$r->transaction;
        $data['payment_method']=$r->payment_method;
        if ($r->payment_method=='null') {
            $data['payment_method']=null;
        }
        $data['pay']=$r->pay;
        $data['total']=$r->total;
    	$data['note']=$r->note;
    	// return $r->all();
    	$validator=Validator::make($data,[
    		'product'=>'required|array',
    		'product.*'=>'required|distinct|regex:/^([0-9]+)$/',
    		'qantities'=>'required|array',
    		'qantities.*'=>'required|regex:/^([0-9.]+)$/',
    		'prices'=>'required|array',
            'prices.*'=>'required|regex:/^([0-9.]+)$/',
			'discounts'=>'nullable|array',
            'discounts.*'=>'nullable|regex:/^([0-9.]+)$/',
            'transport'=>'nullable|regex:/^([0-9]+)$/',
    		'customer'=>'required|regex:/^([0-9]+)$/',
    		'date'=>'required|max:10|date_format:d-m-Y',
    		'issue_date'=>'required|max:10|date_format:d-m-Y',
    		'total_payable'=>'required|max:10|regex:/^([0-9.]+)$/',
    		'total_item'=>'required|max:10|regex:/^([0-9.]+)$/',
    		'discount'=>'nullable|max:15|regex:/^([0-9.]+)$/',
    		'vat'=>'nullable|max:15|regex:/^([0-9.]+)$/',
    		'labour'=>'nullable|max:15|regex:/^([0-9.]+)$/',
            'total'=>'required|max:15|regex:/^([0-9.]+)$/',
            'payment_method'=>'nullable|max:10|regex:/^([0-9]+)$/',
            'transaction'=>'nullable|max:30|regex:/^([a-zA-Z0-9]+)$/',
    		'pay'=>'required|max:18|regex:/^([0-9.]+)$/',
    		'installment_type'=>'required|max:18|regex:/^([0-9]+)$/',
    		'total_installment'=>'required|max:18|regex:/^([0-9]+)$/',
            'minimum_pay_percent'=>'nullable|max:18|regex:/^([0-9.]+)$/',
            'interest'=>'nullable|max:18|regex:/^([0-9.]+)$/',
    		'note'=>'nullable|max:500',
    	]);
    	if ($validator->passes()) {
    		$invoice=new Invoice;
    		$invoice->dates=strtotime(strval($data['date']));
    		$invoice->issue_dates=strtotime(strval($data['issue_date']));
    		$invoice->customer_id=$data['customer'];
    		$invoice->total_item=$data['total_item'];
    		$invoice->discount=$data['discount'];
    		$invoice->vat=$data['vat'];
            $invoice->labour_cost=$data['labour'];
			$invoice->transport=$data['transport_cost'];
			$invoice->transport_id=$data['transport'];
    		$invoice->insmnt_total_days=$data['total_installment'];
    		$invoice->insmnt_type=$data['installment_type'];
            $invoice->total_payable=$data['total_payable'];
    		$invoice->insmnt_pay_percent=$data['minimum_pay_percent'];
    		$invoice->fine=$data['interest'];
            $invoice->total=$data['total'];
            $invoice->action_id=3;
    		$invoice->note=$data['note'];
    		$invoice->user_id=Auth::user()->id;
    		$invoice->save();
    		$inv_id=$invoice->id;
    		$user_id=$invoice->user_id;
	    	if ($invoice=true){
	    			$length=intval($data['total_item'])-1;
    			for ($i=0; $i <=$length;$i++){
	    			$stmt=new Sale();
	                $stmt->invoice_id=$inv_id;
	    			$stmt->dates=strtotime(strval($data['date']));
	    			$stmt->customer_id=$data['customer'];
                    $stmt->product_id=$data['product'][$i+1];
					$stmt->discount=$data['discounts'][$i+1];
                    if ($data['sales_type']!=2) {
                        $stmt->deb_qantity=$data['qantities'][$i+1];
                    }else{
                        $stmt->cred_qantity=$data['qantities'][$i+1];
                    }
	    			$stmt->price=$data['prices'][$i+1];
                    $stmt->user_id=$user_id;
	    			$stmt->action_id=3;
	    			$stmt->save();
    			}
    			if ($stmt=true){
                    if ($data['payment_method']!=null and $data['pay']!=null) {
                        $voucer=new Voucer();
                        $voucer->bank_id=$data['payment_method'];
                        $voucer->dates=strtotime(strval($data['date']));
                        $voucer->nickname='customer';
                        $voucer->data_id=$data['customer'];
                        if ($data['sales_type']!=2) {
                            $voucer->debit=$data['pay'];
                        }else{
                            $voucer->credit=$data['pay'];
                        }
                        $voucer->invoice_id=$inv_id;
                        $voucer->user_id=Auth::user()->id;
                        $voucer->save();
                        $v_id=$voucer->id;
                        $inv=Invoice::where('id',$inv_id)->update(['payment_id'=>$v_id]);
                    return ['message'=>'Invoice and Payment Added Success','id'=>$inv_id];
                    }
                    return ['message'=>'Invoice Added Success','id'=>$inv_id];
    			}
	    	}
    	}
    	return response()->json([$validator->getMessageBag()]);
    }  
}
