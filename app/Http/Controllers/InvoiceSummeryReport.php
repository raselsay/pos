<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use DB;
use Validator;
class InvoiceSummeryReport extends Controller
{
    Public function __construct(){
    	$this->middleware('auth');
    }

    public function Form(){
    	return view('pages.reports.invoice.invoice_summery');
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
                SELECT invoices.id as invoice_id,invoices.dates,IFNULL(customers.name,'NOT AVAILABLE') as name,invoices.total,invoices.total_payable,invoices.action_id from invoices
                     LEFT JOIN customers ON customers.id=invoices.customer_id 
                     where invoices.dates>=:fromDate and invoices.dates<=:toDate and action_id=:type
                ",['fromDate'=>$fromDate,'toDate'=>$toDate,'type'=>$type]);
            return ['get'=>$get,'fromDate'=>$fromDate,'toDate'=>$toDate];
        }
    return response()->json(['errors'=>$validator->getMessageBag()]);
    }
}
