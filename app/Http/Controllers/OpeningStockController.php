<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Invpurchase;
use App\Purchase;
use Auth;

class OpeningStockController extends Controller
{
    public function __construct(){
    	$this->middleware('auth');
    }

    public function Form(){
    	return view('pages.purchase.opening_stock');
    }
    public function Create(Request $r){
    	$data['product']=explode(',',$r->product[0]);      
        $data['store']=explode(',',$r->store[0]);
        $data['qantities']=explode(',',$r->qantities[0]);
        $data['prices']=explode(',',$r->prices[0]);
        $data['date']=strtotime(strval($r->date));
        $data['total_item']=$r->total_item;
        // return $r->all();\
        for ($i=0; $i <=count($data['product'])-1 ; $i++) {
            $count=Purchase::where('product_id',$data['product'][$i])->where('store_id',$data['store'][$i])->get();
            if ($count->count()>0) {
                return response()->json([['product-'.($i)=>['field.'.($i).' product and store already exist']]]);
            }
        }
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
            $invoice->action_id=3;
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
                    $stmt->deb_qantity=$data['qantities'][$i];
                    $stmt->price=$data['prices'][$i];
                    $stmt->user_id=$user_id;
                    $stmt->action_id=3;
                    $stmt->save();
                }
                if ($stmt=true){
                    return ['message'=>'Opening Stock Added Success'];
                }
            }
        }
        return response()->json([$validator->getMessageBag()]);
    }
}
