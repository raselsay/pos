<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use DB;
class WWSReportController extends Controller
{
     public function __construct(){
        $this->middleware('auth');
    }
    public function Form(){
        return view('pages.reports.sales.warehouse_wise_sales');
    }

    public function Report(Request $r){
        $validator=Validator::make($r->all(),[
            'type'=>'required|max:2|regex:/^([0-9]+)$/',
            'store'=>'required|max:15|regex:/^([0-9]+)$/',
            'fromDate'=>'required|max:10|date_format:d-m-Y',
            'toDate'=>'required|max:10|date_format:d-m-Y',
        ]);
        if($validator->passes()){
            $fromDate=strtotime(strval($r->fromDate));
            $toDate=strtotime(strval($r->toDate));
            $data=DB::select("      
            SELECT sales.dates,
                   sales.invoice_id,
                   products.product_name,
                   sum(sales.deb_qantity)+sum(sales.cred_qantity) qantity,
                   sum(sales.price*(sales.deb_qantity+sales.cred_qantity)-sales.price*(sales.deb_qantity-sales.cred_qantity)*sales.discount/100)/(sum(sales.deb_qantity)+sum(sales.cred_qantity)) price from sales
                inner join products on products.id=sales.product_id where sales.dates>=:fromDate and sales.dates <=:toDate and action_id=:action_id and sales.store_id=:id group by sales.product_id
            ",["id"=>$r->store,"action_id"=>$r->type,'fromDate'=>$fromDate,'toDate'=>$toDate]);
            return response()->json(['get'=>$data,'fromDate'=>$fromDate,'toDate'=>$toDate]);
        }
        return response()->json(['errors'=>$validator->getMessageBag()]);
    }
}
