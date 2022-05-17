<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Name;
use App\Information;
use Auth;
use Validator;
use DB;
class HeadWiseSummationReportController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }
    public function Form() {
        $name = Name::select('id', 'name')->get();
        $info = Information::select('company_name', 'adress', 'phone')->first();
        return view('pages.reports.total_calculation.head_wise_summation', compact('name', 'info'));
    }
    public function Report(Request $r) {
        $validator = Validator::make($r->all(), [
            'report_name' => 'required|max:100|min:1',
            'sub_name' => 'required|max:15|min:1',
            'fromDate' => 'required|max:10|date_format:d-m-Y',
            'toDate' => 'required|max:10|date_format:d-m-Y',
        ]);
        if ($validator->passes()) {
            $fromDate = strtotime(strval($r->fromDate));
            $toDate = strtotime(strval($r->toDate));
   $table_name=DB::table('names')->select('table_name','name')->where('name',$r->report_name)->first();
   if ($table_name->table_name==1) {
   	$prev=DB::select("
    SELECT ifnull(sum(voucers.debit),0) debit,ifnull(sum(voucers.credit),0) credit from voucers
    inner join names on voucers.category=names.id and names.name=:report_name
    inner join namerelations on voucers.data_id=namerelations.id where voucers.data_id=:sub_name and voucers.dates<:fromDate
        ", ['report_name' => $r->report_name, 'sub_name' => $r->sub_name, 'fromDate' => $fromDate]);
   $previous_blnc=intval($prev[0]->debit)-intval($prev[0]->credit);
   		$get = DB::select("
    SELECT t1.id,t1.dates,t1.rel_name,t1.debit,t1.credit
    from (
	SELECT voucers.id,voucers.dates,if(names.table_name!=1,names.name,namerelations.rel_name) rel_name,sum(voucers.debit) debit,sum(voucers.credit) credit from voucers
    inner join names on voucers.category=names.id and names.name=:report_name
	inner join namerelations on voucers.data_id=namerelations.id where voucers.data_id=:sub_name and voucers.dates>=:fromDate and voucers.dates<=:toDate group by voucers.data_id
    union all
    SELECT null,null,'previous history','".($previous_blnc>0 ? abs($previous_blnc) : 0)."','".($previous_blnc<0 ? abs($previous_blnc) : 0)."'
    ) t1 order by t1.id
	    ", ['report_name' => $r->report_name, 'sub_name' => $r->sub_name, 'fromDate' => $fromDate, 'toDate' => $toDate]);
   }else{
   		if($table_name->name=='Customer'){
	   	   $cond="or voucers.nickname='customer'";
		   }elseif ($table_name->name=='Supplier') {
		   	   $cond="or voucers.nickname='supplier'";
		   }else{
	   	   $cond="";
		}
   		$prev=DB::select("
	    SELECT ifnull(sum(voucers.debit),0) debit,ifnull(sum(voucers.credit),0) credit from voucers
	    inner join names on voucers.category=names.id and names.name=:report_name
	    inner join ".$table_name->table_name." ON voucers.data_id=".$table_name->table_name.".id where voucers.data_id=:sub_name and voucers.dates<:fromDate ".$cond."

	        ", ['report_name' => $r->report_name, 'sub_name' => $r->sub_name, 'fromDate' => $fromDate]);
	   $previous_blnc=intval($prev[0]->debit)-intval($prev[0]->credit);
	   
	   		$get = DB::select("
	    SELECT t1.id,t1.dates,t1.rel_name,t1.debit,t1.credit
	    from (
		SELECT voucers.id,voucers.dates,if(names.table_name!=1,names.name,".$table_name->table_name.".name) rel_name,sum(voucers.debit) debit,sum(voucers.credit) credit from voucers
	    left join names on voucers.category=names.id and names.name=:report_name
		inner join ".$table_name->table_name." on voucers.data_id=".$table_name->table_name.".id where voucers.data_id=:sub_name and voucers.dates>=:fromDate and voucers.dates<=:toDate ".$cond."
		group by voucers.data_id
	    union all
	    SELECT null,null,'previous history','".($previous_blnc>0 ? abs($previous_blnc) : 0)."','".($previous_blnc<0 ? abs($previous_blnc) : 0)."'
	    ) t1 order by t1.id
	    ", ['report_name' => $r->report_name, 'sub_name' => $r->sub_name, 'fromDate' => $fromDate, 'toDate' => $toDate]);
   }
            return response()->json(['get' => $get, 'fromDate' => $fromDate, 'toDate' => $toDate,'previous_balance'=>$previous_blnc]);
        }
        return response()->json(['error' => $validator->getMessageBag()]);
    }
}
