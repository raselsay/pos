<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Validator;
class PWPReportController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    public function Form(){
        return view('pages.reports.purchase.product_wise_purchase');
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
            SELECT purchases.dates,products.product_name,sum(purchases.deb_qantity)+(purchases.cred_qantity) qantity,purchases.price,purchases.invoice_id
             from purchases
                inner join products on products.id=purchases.product_id
                where purchases.product_id=:id and purchases.dates >= :fromDate and purchases.dates <= :toDate
                and purchases.action_id=:action_id
                group by purchases.dates,purchases.invoice_id,purchases.product_id and purchases.action_id=:action_id
            ",["id"=>$r->product,"action_id"=>$r->type,'fromDate'=>$fromDate,'toDate'=>$toDate]);
            return response()->json(['get'=>$data,'fromDate'=>$fromDate,'toDate'=>$toDate]);
        }
        return response()->json($validator->getMessageBag());
    }
}
