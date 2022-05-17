<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use DB;
class CustomerWisePaymentReportController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    public function Form(){
    	return view('pages.reports.buyer.customer_wise_payment');
    }
    public function Report(Request $r){
        $validator=Validator::make($r->all(),[
            'fromDate'=>'required|max:10|date_format:d-m-Y',
            'toDate'=>'required|max:10|date_format:d-m-Y',
        ]);
        if($validator->passes()){
            $fromDate=strtotime(strval($r->fromDate));
            $toDate=strtotime(strval($r->toDate));
            $data=DB::select("
            SELECT customers.id,customers.name,customers.phone1,customers.adress,ifnull(spos.name,'X') as spo_name,ifnull(groups.name,'X') as group_name,(ifnull((select sum(voucers.debit-voucers.credit) from voucers
left join names on voucers.category=names.id
      WHERE  ((voucers.data_id=customers.id and voucers.nickname='customer') or (voucers.data_id=customers.id and names.table_name='customers'))
      and voucers.dates>=:fromDate and voucers.dates<=:toDate
),0)) total from customers
left join spos on spos.id=customers.spo_id
left join groups on groups.id=customers.group_types
order by customers.id asc

            ",['fromDate'=>$fromDate,'toDate'=>$toDate]);

            return response()->json(['get'=>$data,'fromDate'=>$fromDate,'toDate'=>$toDate]);
        }
        return response()->json(['errors'=>$validator->getMessageBag()]);
    }
}
