<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use DB;
class StockSummeryReportController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    public function Form(){
        return view('pages.reports.stock.stock_summery');
    }
    public function Report(Request $r){
        $validator=Validator::make($r->all(),[
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
                    where purchases.dates<:fromDate and purchases.product_id=:product_id and purchases.action_id!=1
                    ) as t1
                    left join (
                    SELECT sales.product_id,ifnull(SUM(sales.deb_qantity-sales.cred_qantity),0) as qty from sales
                    where sales.dates<:fromDate and sales.product_id=:product_id and sales.action_id!=1
                    group by sales.product_id
                    ) as t2 on t1.product_id=t2.product_id
            	",['fromDate'=>$fromDate,'product_id'=>$r->product]);
           if (count($previous)>0) {
           		$debit=abs($previous[0]->d_qty);
           		$credit=abs($previous[0]->c_qty);
           }else{
           	   	$debit=0;
           		$credit=0;
           }
            $data=DB::select("
           SELECT t1.name,t1.qty
            from(
            SELECT 'Prev-Stock' name,'".($debit-$credit)."' qty
            UNION ALL
            SELECT 'Opening-Stock',ifnull(sum(purchases.deb_qantity-purchases.cred_qantity),0.00) qty
            from purchases
            where purchases.product_id=:product_id  and purchases.dates>=:fromDate and purchases.dates<=:toDate and purchases.action_id=3
            UNION ALL
            SELECT 'Purchase',ifnull(sum(purchases.deb_qantity-purchases.cred_qantity),0.00) qty
            from purchases
            where purchases.product_id=:product_id  and purchases.dates>=:fromDate and purchases.dates<=:toDate and (purchases.action_id=0 or purchases.action_id=4)
            UNION ALL
            SELECT 'Purchase Return' as name,ifnull(sum(purchases.cred_qantity),0.00) debit
            from purchases
            where purchases.product_id=:product_id and purchases.dates>=:fromDate and purchases.dates<=:toDate and purchases.action_id=2 
            UNION ALL
            SELECT 'Damage-Out' as name,ifnull(sum(purchases.cred_qantity),0.00) debit
            from purchases
            where purchases.product_id=:product_id and purchases.dates>=:fromDate and purchases.dates<=:toDate and purchases.action_id=5
            UNION ALL
            SELECT 'Sale',ifnull(sum(sales.deb_qantity),0.00) from sales
            where sales.product_id=:product_id and sales.dates>=:fromDate and sales.dates<=:toDate and sales.action_id!=1
            UNION ALL
            SELECT 'Sale Return',ifnull(sum(sales.cred_qantity),0.00) from sales
            where sales.product_id=:product_id and sales.dates>=:fromDate and sales.dates<=:toDate and sales.action_id!=1
            ) t1
            ",["product_id"=>$r->product,'fromDate'=>$fromDate,'toDate'=>$toDate]);
            return response()->json(['get'=>$data,'fromDate'=>$fromDate,'toDate'=>$toDate]);
        }
        return response()->json($validator->getMessageBag());
    }
}
