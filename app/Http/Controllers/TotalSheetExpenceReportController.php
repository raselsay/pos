<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Name;
use App\Information;
use Auth;
use DB;
use Validator;
class TotalSheetExpenceReportController extends Controller
{
     public function __construct() {
        $this->middleware('auth');
    }
    public function Form() {
        $name = Name::select('id', 'name')->get();
        $info = Information::select('company_name', 'adress', 'phone')->first();
        return view('pages.reports.total_calculation.total_expence_summation', compact('name', 'info'));
    }
    public function Report(Request $r) {
        $validator = Validator::make($r->all(), [
            'fromDate' => 'required|max:10|date_format:d-m-Y',
            'toDate' => 'required|max:10|date_format:d-m-Y',
        ]);
        if ($validator->passes()) {
            $fromDate = strtotime(strval($r->fromDate));
            $toDate = strtotime(strval($r->toDate));
   		$get = DB::select("
    SELECT t1.rel_name,t1.credit
    from (
    SELECT 0.1 as serials,'Customer' as rel_name,sum(voucers.credit) credit from voucers
    left join names on voucers.category=names.id
    left join customers on ((voucers.category=names.id and names.table_name='customers') or voucers.nickname='customer') and voucers.data_id=customers.id
    where voucers.dates>=:fromDate and voucers.dates<=:toDate and voucers.data_id=customers.id 
    union All 
    SELECT 0.2,'Supplier',ifnull(sum(voucers.credit),0) credit from voucers
    left join names on voucers.category=names.id
    left join suppliers on ((voucers.category=names.id and names.table_name='suppliers') or voucers.nickname='supplier') and voucers.data_id=suppliers.id
    where voucers.dates>=:fromDate and voucers.dates<=:toDate and voucers.data_id=suppliers.id
    union All
    SELECT 0.3,names.name,sum(voucers.credit) credit from voucers
    left join names on voucers.category=names.id
    left join employees on (voucers.category=names.id and names.table_name='employees') and voucers.data_id=employees.id
    where voucers.dates>=:fromDate and voucers.dates<=:toDate and voucers.data_id=employees.id
    union All
    SELECT 0.4,names.name,sum(voucers.credit) credit from voucers
    left join names on voucers.category=names.id
    left join transports on (voucers.category=names.id and names.table_name='transports') and voucers.data_id=transports.id
    where voucers.dates>=:fromDate and voucers.dates<=:toDate and voucers.data_id=transports.id
    union All
	SELECT 0.5,namerelations.rel_name,sum(voucers.credit) credit from voucers
    inner join names on voucers.category=names.id
	inner join namerelations on voucers.data_id=namerelations.id where voucers.dates>=:fromDate and voucers.dates<=:toDate and names.table_name=1
     group by namerelations.rel_name 
    ) t1 order by t1.serials", ['fromDate' => $fromDate, 'toDate' => $toDate]);
            return response()->json(['get' => $get, 'fromDate' => $fromDate, 'toDate' => $toDate]);
        }
        return response()->json(['error' => $validator->getMessageBag()]);
    }
}
