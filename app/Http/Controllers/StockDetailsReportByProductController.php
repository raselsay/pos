<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use DB;
class StockDetailsReportByProductController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    public function Form(){
        return view('pages.reports.stock.stock_details_by_product');
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
            $data=DB::select("
            SELECT purchases.dates,stores.name as store_name,ifnull(sum(purchases.deb_qantity),0.00) qty
            from purchases
            inner join stores on stores.id=purchases.store_id
            where purchases.product_id=:product_id
            and purchases.action_id!=1 and purchases.dates>=:fromDate and purchases.dates<=:toDate
            group by purchases.dates,purchases.store_id
            ",["product_id"=>$r->product,'fromDate'=>$fromDate,'toDate'=>$toDate]);
            return response()->json(['get'=>$data,'fromDate'=>$fromDate,'toDate'=>$toDate]);
        }
        return response()->json($validator->getMessageBag());
    }
}
