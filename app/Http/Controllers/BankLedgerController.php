<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Name;
use App\Information;
use Validator;
use DB;
class BankLedgerController extends Controller
{
    public function __construct(){
    	$this->middleware('auth');
    }
    public function Form(){
    	 $name = Name::select('id', 'name')->where('stutus', 0)->get();
        $info = Information::select('company_name', 'adress', 'phone')->first();
    	return view('pages.reports.bank_ledger.bank_ledger',compact('name','info'));
    }
    public function Report(Request $r){
    	$validator=Validator::make($r->all(),[
    		'bank'=>'required|max:50',
    		'fromDate'=>'required|max:50|date_format:d-m-Y',
    		'toDate'=>'required|max:50|date_format:d-m-Y',
    	]);

    	if ($validator->passes()) {
    $fromDate=strtotime($r->fromDate);
    $toDate=strtotime($r->toDate);
    $op=DB::table('banks')->selectRaw('opening_balance op_blnc')->where('id',$r->bank)->first();
    $prev_voucer=DB::table('voucers')->selectRaw('ifnull(sum(debit)-sum(credit),0) total')->whereRaw('dates < ? and bank_id=?',[$fromDate,$r->bank])->first();
    $previous=intval($op->op_blnc)+intval($prev_voucer->total);
    		$get=DB::select("
SELECT t.id,t.dates,t.bank_name,t.category,t.name,t.Deposit,t.Expence
from (SELECT 
             voucers.id,
             voucers.dates,
             banks.name bank_name,
             concat(ifnull(names.name,''),ifnull(voucers.nickname,'')) category,
             concat(ifnull(suppliers.name,''),ifnull(customers.name,''),ifnull(employees.name,''),ifnull(transports.name,''),ifnull(banks2.name,''),ifnull(namerelations.rel_name,'')) as name,
  ifnull(voucers.debit,0) as Deposit,
  ifnull(voucers.credit,0) as Expence
       from voucers
          left join names on voucers.category=names.id
          left join banks on voucers.bank_id=banks.id
          left join suppliers on ((voucers.category=names.id and names.table_name='suppliers') or voucers.nickname='supplier') and voucers.data_id=suppliers.id 
          left join customers on ((voucers.category=names.id and names.table_name='customers') or voucers.nickname='customer') and voucers.data_id=customers.id
          left join employees on ((voucers.category=names.id and names.table_name='employees') or voucers.nickname='employee') and voucers.data_id=employees.id
          left join transports on ((voucers.category=names.id and names.table_name='transports') or voucers.nickname='transport') and voucers.data_id=transports.id
          left join banks as banks2 on (voucers.nickname='Balance Transfered' or voucers.nickname='Balance Received') and voucers.data_id=banks2.id
          -- left join names on voucers.category=names.name and names.stutus=0
          left join namerelations on names.id=namerelations.name_id and voucers.data_id=namerelations.id
        where voucers.dates>=:fromDate and voucers.dates<=:toDate and banks.id=:bank
        UNION ALL
  SELECT '','','','','Previous Balance','".($previous>0 ? abs($previous) : 0 )."','".($previous<0 ? abs($previous) : 0 )."') t
order by t.dates,t.id
    	    		",['bank'=>$r->bank,'fromDate'=>$fromDate,'toDate'=>$toDate]);
    		return response()->json(['get'=>$get,'fromDate'=>$fromDate,'toDate'=>$toDate]);
    	}
    	
    }
}
