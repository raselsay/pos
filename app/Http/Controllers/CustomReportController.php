<?php

namespace App\Http\Controllers;

use App\Information;
use App\Name;
use DB;
use Illuminate\Http\Request;
use Validator;

class CustomReportController extends Controller {
    public function __construct() {
        $this->middleware('auth');
    }
    public function Form() {
        $name = Name::select('id', 'name')->where('stutus', 0)->get();
        $info = Information::select('company_name', 'adress', 'phone')->first();
        return view('pages.reports.custom_report.custom_report', compact('name', 'info'));
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
    $prev=DB::select("
    SELECT ifnull(sum(voucers.debit),0) debit,ifnull(sum(voucers.credit),0) credit from voucers
    inner join names on voucers.category=names.id and names.name=:report_name
    inner join namerelations on voucers.data_id=namerelations.id where voucers.data_id=:sub_name and voucers.dates<:fromDate
        ", ['report_name' => $r->report_name, 'sub_name' => $r->sub_name, 'fromDate' => $fromDate]);
   $previous_blnc=intval($prev[0]->debit)-intval($prev[0]->credit);
            $get = DB::select("
    select t1.id,t1.dates,t1.rel_name,t1.debit,t1.credit
    from (
	SELECT voucers.id,voucers.dates,if(names.table_name!=1,names.name,namerelations.rel_name) rel_name,voucers.debit,voucers.credit from voucers
    inner join names on voucers.category=names.id and names.name=:report_name
	inner join namerelations on voucers.data_id=namerelations.id where voucers.data_id=:sub_name and voucers.dates>=:fromDate and voucers.dates<=:toDate
    union all
    SELECT null,null,'previous history','".($previous_blnc>0 ? abs($previous_blnc) : 0)."','".($previous_blnc<0 ? abs($previous_blnc) : 0)."'
    ) t1 order by t1.id
	    ", ['report_name' => $r->report_name, 'sub_name' => $r->sub_name, 'fromDate' => $fromDate, 'toDate' => $toDate]);
            return response()->json(['get' => $get, 'fromDate' => $fromDate, 'toDate' => $toDate,'previous_balance'=>$previous_blnc]);
        }
        return response()->json(['error' => $validator->getMessageBag()]);
    }
}
