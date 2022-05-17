<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Validator;
class CustomerWiseTotalSaleReportController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    public function Form(){
    	return view('pages.reports.buyer.customer_wise_total_sale');
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
            SELECT   customers.name,customers.adress,customers.phone1,spos.name as spo_name,ifnull(groups.name,'') as group_name,(select ifnull(sum(deb_qantity)+sum(cred_qantity),0) qantity from sales where customers.id=sales.customer_id AND sales.product_id=:product and sales.dates >= :fromDate and sales.dates <= :toDate and sales.action_id=:action_id) qantity,
            cast((select ifnull((ifnull((sum((deb_qantity+cred_qantity)*price)),0)/ifnull(sum(deb_qantity+cred_qantity),0))*ifnull(sum(deb_qantity+cred_qantity),0),0) from sales where customers.id=sales.customer_id AND sales.product_id=:product and sales.dates >= :fromDate and sales.dates <= :toDate and sales.action_id=:action_id) as decimal(20,2)) total
             from customers
             left join spos on spos.id=customers.spo_id
             left join groups on groups.id=customers.group_types
            ",["product"=>$r->product,"action_id"=>$r->type,'fromDate'=>$fromDate,'toDate'=>$toDate]);
            return response()->json(['get'=>$data,'fromDate'=>$fromDate,'toDate'=>$toDate]);
        }
        return response()->json(['errors'=>$validator->getMessageBag()]);
    }
}
