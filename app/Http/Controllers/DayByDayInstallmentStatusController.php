<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use DataTables;
class DayByDayInstallmentStatusController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    public function Form(){
        if (request()->ajax()) {
            $get=DB::select("
                select invoices.total_payable as total_payable,
                    customers.name,
                    customers.phone1,
                    ifnull(count(voucers.id),0) paid,
                    invoices.insmnt_total_days days,
                    invoices.insmnt_type,
                    invoices.issue_dates
                from invoices
                inner join customers on customers.id=invoices.customer_id
                left join voucers on invoices.id=voucers.invoice_id and voucers.pay_action_id=1
                where invoices.action_id=3 group by invoices.id,voucers.invoice_id
                having paid<days
            ");
            foreach($get as $gets){
                if($gets->insmnt_type==1){
                    $date=strtotime("+".($gets->paid)." month",intval($gets->issue_dates));
                    if ($date==strtotime(date('d-m-Y')) or $date<strtotime(date('d-m-Y'))) {
                        $data[]=[
                            'name'         =>$gets->name,
                            'phone1'         =>$gets->phone1,
                            'total_payable'=>$gets->total_payable,
                            'paid'         =>$gets->paid,
                            'total_inst'   =>$gets->days,
                            'pay_date'     =>$date,
                        ];
                    }
                }elseif($gets->insmnt_type==0){
                    $date=strtotime("+".(intval($gets->paid)*7)." day",intval($gets->issue_dates));
                    if ($date==strtotime(date('d-m-Y')) or $date<strtotime(date('d-m-Y'))) {
                        $data[]=[
                            'name'         =>$gets->name,
                            'total_payable'=>$gets->total_payable,
                            'paid'         =>$gets->paid,
                            'total_inst'   =>$gets->days,
                            'pay_date'     =>$date,
                        ];
                    }
                }
            }
            $allData= isset($data) ? $data : [] ;
            return DataTables::of($allData)
              ->addIndexColumn()
              ->addColumn('pay_date',function ($allData){
                return date('d-m-Y',$allData['pay_date']);
              })->setRowAttr(['style'=>
              function ($allData) {
                if (intval($allData['pay_date'])<strtotime(date('d-m-Y'))) {
                    return 'color:red;';
                }else{
                    return "color:green;";
                }
            },
             ])
          ->rawColumns(['action'])->make(true);
         }
         return view('pages.installment.today');
    }
}
