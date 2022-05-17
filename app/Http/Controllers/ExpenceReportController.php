<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
class ExpenceReportController extends Controller
{
    public function __construct(){
    	$this->middleware('auth');
    }

    public function Form(){
    	return view('pages.reports.accounts.expence_report');
    }
    public function Report(Request $r){
    	$fromDate=strtotime(strval($r->fromDate));
    	$toDate=strtotime(strval($r->toDate));
        
        $opening_balance=DB::table('banks')->selectRaw('sum(opening_balance) as opening_balance')->first();
        $voucer=DB::table('voucers')->selectRaw('sum(debit) as debit,sum(credit) as credit')->first();
        $total=($voucer->debit-$voucer->credit)+$opening_balance->opening_balance;
    	$get=DB::select("
  SELECT t.id,t.dates,t.bank_name,t.category,t.name,t.Deposit,t.Expence
from (SELECT 
             voucers.id,
             voucers.dates,
             banks.name bank_name,
             concat(ifnull(names.name,''),ifnull(voucers.nickname,'')) category,
             concat(ifnull(suppliers.name,''),ifnull(customers.name,''),ifnull(banks2.name,''),ifnull(namerelations.rel_name,'')) as name,
  ifnull(sum(voucers.debit),0) as Deposit,
  ifnull(sum(voucers.credit),0) as Expence
       from voucers
          left join names on voucers.category=names.id
          left join banks on voucers.bank_id=banks.id
          left join suppliers on ((voucers.category=names.id and names.table_name='suppliers') or voucers.nickname='supplier') and voucers.data_id=suppliers.id 
          left join customers on ((voucers.category=names.id and names.table_name='customers') or voucers.nickname='customer') and voucers.data_id=customers.id
          left join banks as banks2 on (voucers.nickname='Balance Transfered' or voucers.nickname='Balance Received') and voucers.data_id=banks2.id
          -- left join names on voucers.category=names.name and names.stutus=0
          left join namerelations on names.id=namerelations.name_id and voucers.data_id=namerelations.id
        where voucers.dates>=:fromDate and voucers.dates<=:toDate
        group by voucers.category,voucers.nickname ) t
order by t.dates,t.id
    	    		",['fromDate'=>$fromDate,'toDate'=>$toDate]);

    	return response()->json(['get'=>$get,'fromDate'=>$fromDate,'toDate'=>$toDate,'total'=>$total]);
    }
}
