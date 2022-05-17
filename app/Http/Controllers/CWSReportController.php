<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use DB;
class CWSReportController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    public function Form(){
    	return view('pages.reports.sales.customer_wise_sales');
    }
    public function Report(Request $r){
        $validator=Validator::make($r->all(),[
            'type'=>'required|max:2|regex:/^([0-9]+)$/',
            'customer'=>'required|max:15|regex:/^([0-9]+)$/',
            'fromDate'=>'required|max:10|date_format:d-m-Y',
            'toDate'=>'required|max:10|date_format:d-m-Y',
        ]);
        if($validator->passes()){
            $fromDate=strtotime(strval($r->fromDate));
            $toDate=strtotime(strval($r->toDate));
            $data=DB::select("
            SELECT sales.dates,products.product_name,sum(sales.deb_qantity+sales.cred_qantity) qantity,cast(sales.price-(sales.price*sales.discount)/100 as decimal(20,2)) price,sales.invoice_id
             from sales
                inner join products on products.id=sales.product_id
                where sales.customer_id=:id and sales.dates >= :fromDate and sales.dates <= :toDate
                and sales.action_id=:action_id
                group by sales.id,sales.dates
            ",["id"=>$r->customer,"action_id"=>$r->type,'fromDate'=>$fromDate,'toDate'=>$toDate]);
            return response()->json(['get'=>$data,'fromDate'=>$fromDate,'toDate'=>$toDate]);
        }
        return response()->json($validator->getMessageBag());
    }
}
