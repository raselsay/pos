<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use DB;
class PurchaseSummeryReportController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    public function Form(){
        return view('pages.reports.purchase.purchase');
    }
    public function Report(Request $r){
        $validator=Validator::make($r->all(),[
            'type'=>'required|max:15',
            'fromDate'=>'required|max:10|min:10|date_format:d-m-Y',
            'toDate'=>'required|max:10|min:10|date_format:d-m-Y',
        ]);
        if ($validator->passes()) {
            $type=$r->type;
            $fromDate=strtotime(strval(trim($r->fromDate)));
            $toDate=strtotime(strval(trim($r->toDate)));
            $get=DB::select("
                SELECT purchases.dates,
       products.product_name,
       sum(purchases.deb_qantity+purchases.cred_qantity) qantity,
       sum(purchases.price*(purchases.deb_qantity+purchases.cred_qantity))/sum(purchases.deb_qantity)+sum(purchases.cred_qantity) price
        from purchases
            inner join products on products.id=purchases.product_id 
                where purchases.dates>= :fromDate and purchases.dates <=:toDate and purchases.action_id=:type group by purchases.product_id
                ",['fromDate'=>$fromDate,'toDate'=>$toDate,'type'=>$type]);
            return ['get'=>$get,'fromDate'=>$fromDate,'toDate'=>$toDate];
        }
    return response()->json(['errors'=>$validator->getMessageBag()]);
    }
}
