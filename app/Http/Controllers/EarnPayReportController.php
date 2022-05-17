<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
class EarnPayReportController extends Controller
{
    public function __construct(){
    	$this->middleware('auth');
    }

    public function Form(){
    	return view('pages.reports.total_calculation.earn_pay');
    }
   public function Report(Request $r){
    	$fromDate=strtotime(strval($r->fromDate));
    	$toDate=strtotime(strval($r->toDate));
        
        $opening_balance=DB::table('banks')->selectRaw('sum(opening_balance) as opening_balance')->first();
        $previous=DB::table('voucers')->selectRaw('sum(debit) debit,sum(credit) as credit')->whereRaw("dates<?",[$fromDate])->first();
        $previous_debit=$previous->debit+$opening_balance->opening_balance;
            $deb=abs($previous_debit);
            $cred=abs($previous->credit);
            $total_prev=$deb-$cred;
            if ($total_prev<0) {
              $cred=abs($total_prev);
              $deb=0;
            }else{
              $cred=0;
              $deb=abs($total_prev);
            }
    	$get=DB::select("
   SELECT 'Previous Balance' as name,ifnull('".$total_prev."',0) as total
   UNION ALL
   SELECT 'Income', ifnull(sum(debit),0) from voucers
   where (pay_action_id <> 2 OR pay_action_id IS NULL) and dates>=:fromDate and dates<=:toDate
   UNION ALL 
   SELECT 'Pay',ifnull(sum(credit),0) from voucers
   where (pay_action_id <> 2 OR pay_action_id IS NULL) and dates>=:fromDate and dates<=:toDate
    	",['fromDate'=>$fromDate,'toDate'=>$toDate]);
    	return response()->json(['get'=>$get,'fromDate'=>$fromDate,'toDate'=>$toDate]);
    }
}
