<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Auth;
use App\Invpurchasebacks;
use App\Purchasebacks;
use DB;
class PurchaseReturn extends Controller
{
    public function __construct(){
    	$this->middleware('auth');
    }
    public function PurchaseForm(){
    	return view('pages.purchase.purchaseReturn');
    }
    public function insertReturn(Request $r){
   		$data['product']=explode(',',$r->product[0]);
    	$data['qantities']=explode(',',$r->qantities[0]);
    	$data['prices']=explode(',',$r->prices[0]);
    	$data['supplier']=$r->supplier;
    	$data['date']=strtotime(strval($r->date));
    	$data['total_payable']=$r->total_payable;
    	$data['total_item']=$r->total_item;
    	$data['transport']=$r->transport;
    	$data['labour']=$r->labour;
    	$data['fine']=$r->fine;
    	$data['total']=$r->total;
    	// return $r->all();
    	$validator=Validator::make($data,[
    		'product'=>'required|array',
    		'product.*'=>'required|distinct|regex:/^([0-9]+)$/',
    		'qantities'=>'required|array',
    		'qantities.*'=>'required|regex:/^([0-9]+)$/',
    		'prices'=>'required|array',
    		'prices.*'=>'required|regex:/^([0-9]+)$/',
    		'supplier'=>'required|regex:/^([0-9]+)$/',
    		'date'=>'required|max:10',
    		'total_payable'=>'required|max:10',
    		'total_item'=>'required|max:10',
    		'transport'=>'nullable|max:15',
    		'labour'=>'nullable|max:15',
    		'fine'=>'nullable|max:15',
    		'total'=>'required|max:15',
    	]);
    	if ($validator->passes()) {
    		$invoice=new Invpurchasebacks;
    		$invoice->dates=$data['date'];
    		$invoice->supplier_id=$data['supplier'];
    		$invoice->total_item=$data['total_item'];
    		$invoice->fine=$data['fine'];
    		$invoice->transport=$data['transport'];
    		$invoice->labour_cost=$data['labour'];
    		$invoice->total_payable=$data['total_payable'];
    		$invoice->total=$data['total'];
    		$invoice->increment_id=$this->Increment();
    		$invoice->user_id=Auth::user()->id;
    		$invoice->save();
    		$inv_id=$invoice->id;
    		$user_id=$invoice->user_id;
	    	if ($invoice=true) {
	    			$length=intval($data['total_item'])-1;
    			for ($i=0; $i <=$length; $i++) {
	    			$stmt=new Purchasebacks();
	                $stmt->invoice_id=$inv_id;
	    			$stmt->dates=$data['date'];
	    			$stmt->supplier_id=$r->supplier;
	    			$stmt->product_id=$data['product'][$i];
	    			$stmt->qantity=$data['qantities'][$i];
	    			$stmt->price=$data['prices'][$i];
	    			$stmt->user_id=$user_id;
	                $stmt->increment_id=$this->Increment()+1;
	    			$stmt->save();
    			}
    			if ($stmt=true) {
    				$increment_id=$this->Increment()+1;
    				$inv=Invpurchasebacks::where('id',$inv_id)->update(['increment_id'=>$increment_id]);
    				if ($inv=true) {
    					return ['message'=>'success'];
    				}
    			}
	    	}
    	}
    	return response()->json([$validator->getMessageBag()]);
    }
    private function Increment(){
       $data=DB::select("
          SELECT 
              (SELECT max(increment_id) from voucers) as voucer_id,
              (SELECT max(increment_id) from invoices) as invoice_id,
              (SELECT max(increment_id) from sales) as sales_id,
              (SELECT max(increment_id) from invoicebacks) as invoiebacks_id,
              (SELECT max(increment_id) from salesbacks) as salesbacks_id,
              (SELECT max(increment_id) from invpurchases) as invpurchase_id,
              (SELECT max(increment_id) from purchases) as purchase_id,
              (SELECT max(increment_id) from invpurchasebacks) as invpurchasebacks_id,
              (SELECT max(increment_id) from purchasebacks) as purchaseback_id
              ");
        foreach ($data[0] as $key => $value) {
            $arr[]=$value;
        }
        return intval(max($arr));
    }
}
