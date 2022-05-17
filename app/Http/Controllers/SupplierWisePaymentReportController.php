<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use DB;
class SupplierWisePaymentReportController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    public function Form(){
    	return view('pages.reports.supplier.supplier_wise_payment');
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
            SELECT suppliers.id,suppliers.name,suppliers.phone,suppliers.adress,(ifnull((select sum(voucers.credit-voucers.debit) from voucers
left join names on voucers.category=names.id
      WHERE  ((voucers.data_id=suppliers.id and voucers.nickname='supplier') or (voucers.data_id=suppliers.id and names.table_name='suppliers'))
      and voucers.dates>=:fromDate and voucers.dates<=:toDate
),0)) total from suppliers
order by suppliers.id asc
            ", ['fromDate'=>$fromDate,'toDate'=>$toDate]);
            return response()->json(['get'=>$data,'fromDate'=>$fromDate,'toDate'=>$toDate]);
        }
        return response()->json(['errors'=>$validator->getMessageBag()]);
    }
}
