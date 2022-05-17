<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Validator;
class PWSReportController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    public function Form(){
        return view('pages.reports.sales.product_wise_sales');
    }

    public function Report(Request $r){
        $validator=Validator::make($r->all(),[
            'type'=>'required|max:2|regex:/^([0-9]+)$/',
            'product'=>'required|max:15|regex:/^([0-9]+)$/',
            'fromDate'=>'required|max:10|date_format:d-m-Y',
            'toDate'=>'required|max:10|date_format:d-m-Y',
        ]);
        if($validator->passes()){
            $fromDate=strtotime(strval($r->fromDate));
            $toDate=strtotime(strval($r->toDate));
            $data=DB::select("
            SELECT sales.dates,customers.name,sum(sales.deb_qantity+sales.cred_qantity) qantity,cast(sales.price-(sales.price*sales.discount)/100 as decimal(20,2)) price,sales.invoice_id
             from sales
                inner join customers on customers.id=sales.customer_id
                where sales.product_id=:id and sales.dates >= :fromDate and sales.dates <= :toDate
                and sales.action_id=:action_id
                group by sales.id,sales.invoice_id,sales.product_id
            ",["id"=>$r->product,"action_id"=>$r->type,'fromDate'=>$fromDate,'toDate'=>$toDate]);
            return response()->json(['get'=>$data,'fromDate'=>$fromDate,'toDate'=>$toDate]);
        }
        return response()->json($validator->getMessageBag());
    }
}
