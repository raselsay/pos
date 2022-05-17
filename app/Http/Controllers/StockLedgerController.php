<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Validator;

class StockLedgerController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    public function Form(){
        return view('pages.reports.stock.stock_ledger');
    }
    public function Report(Request $r){
        $validator=Validator::make($r->all(),[
            'store'=>'required|max:25|regex:/^([0-9]+)$/',
            'product'=>'required|max:15|regex:/^([0-9]+)$/',
            'fromDate'=>'required|max:10|date_format:d-m-Y',
            'toDate'=>'required|max:10|date_format:d-m-Y',
        ]);
        if($validator->passes()){
            $fromDate=strtotime(strval($r->fromDate));
            $toDate=strtotime(strval($r->toDate));
           $previous=DB::select("
            	SELECT  ifnull(t1.qty,0) d_qty,ifnull(t2.qty,0) c_qty from
            		(
            		SELECT purchases.product_id,ifnull(SUM(purchases.deb_qantity-purchases.cred_qantity),0) as qty from purchases
            		where purchases.dates<:fromDate and purchases.store_id=:store_id and purchases.product_id=:product_id and purchases.action_id!=1
            		) as t1
            		left join (
            		SELECT sales.product_id,ifnull(SUM(sales.deb_qantity-sales.cred_qantity),0) as qty from sales
            		where sales.dates<:fromDate and sales.store_id=:store_id and sales.product_id=:product_id and sales.action_id!=1
            		group by sales.product_id
            		) as t2 on t1.product_id=t2.product_id
            	",['fromDate'=>$fromDate,'product_id'=>$r->product,'store_id'=>$r->store]);
           if (count($previous)>0) {
           		$debit=abs($previous[0]->d_qty);
           		$credit=abs($previous[0]->c_qty);
           }else{
           	   	$debit=0;
           		$credit=0;
           }
            $data=DB::select("
            SELECT t1.series, t1.dates,t1.tab,t1.invoice_id,t1.name,t1.deb_qantity,t1.cred_qantity,t1.action_id from(
            SELECT if(purchases.action_id=3,purchases.dates+0.1,purchases.dates) series,purchases.dates,1 tab,purchases.invoice_id,suppliers.name,purchases.deb_qantity,purchases.cred_qantity,purchases.action_id
            from purchases
            left join suppliers on suppliers.id=purchases.supplier_id
            where purchases.product_id=:product_id and purchases.store_id=:store_id
            and purchases.action_id!=1 and purchases.dates>=:fromDate and purchases.dates<=:toDate
            UNION ALL
            SELECT sales.dates+0.2,sales.dates,2,sales.invoice_id,customers.name,sales.cred_qantity,sales.deb_qantity,sales.action_id from sales
            left join customers on customers.id=sales.customer_id
            where sales.product_id=:product_id and sales.store_id=:store_id
            and sales.action_id!=1 and sales.dates>=:fromDate and sales.dates<=:toDate
            UNION ALL
            SELECT '','','','',null,'".$debit."','".$credit."',''
            ) t1 order by t1.series,t1.invoice_id,t1.dates
            ",["product_id"=>$r->product,"store_id"=>$r->store,'fromDate'=>$fromDate,'toDate'=>$toDate]);
            return response()->json(['get'=>$data,'fromDate'=>$fromDate,'toDate'=>$toDate]);
        }
        return response()->json($validator->getMessageBag());
    }
}