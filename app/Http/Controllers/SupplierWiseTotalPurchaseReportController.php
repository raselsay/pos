<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use DB;
class SupplierWiseTotalPurchaseReportController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    public function Form(){
    	return view('pages.reports.supplier.supplier_wise_total_purchase');
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
            SELECT   suppliers.name,(select ifnull(sum(deb_qantity)+sum(cred_qantity),0) qantity from purchases where suppliers.id=purchases.supplier_id AND purchases.product_id=:product and purchases.dates >= :fromDate and purchases.dates <= :toDate and purchases.action_id=:action_id) qantity,
            cast((select ifnull((ifnull((sum((deb_qantity+cred_qantity)*price)),0)/ifnull(sum(deb_qantity+cred_qantity),0))*ifnull(sum(deb_qantity+cred_qantity),0),0) from purchases where suppliers.id=purchases.supplier_id AND purchases.product_id=:product and purchases.dates >= :fromDate and purchases.dates <= :toDate and purchases.action_id=:action_id) as decimal(20,2)) total
             from suppliers
            ",["product"=>$r->product,"action_id"=>$r->type,'fromDate'=>$fromDate,'toDate'=>$toDate]);
            return response()->json(['get'=>$data,'fromDate'=>$fromDate,'toDate'=>$toDate]);
        }
        return response()->json(['errors'=>$validator->getMessageBag()]);
    }
}
