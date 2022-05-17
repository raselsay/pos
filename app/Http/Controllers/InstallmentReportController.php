<?php

namespace App\Http\Controllers;

use App\Information;
use DB;

class InstallmentReportController extends Controller {
    public function __construct() {
        $this->middleware('auth');
    }
    public function Form() {
        $info = Information::select('company_name', 'adress', 'phone')->first();
        return view('pages.reports.installment.installment_report', compact('info'));
    }
    public function getReport($id = null) {
        $id = (explode('|', $id))[0];
        $invoice = DB::select("
SELECT invoices.id,invoices.dates,invoices.issue_dates,customers.name,invoices.total_item,invoices.total_payable,invoices.insmnt_type,invoices.insmnt_total_days,invoices.insmnt_pay_percent from invoices
inner join customers on invoices.customer_id=customers.id
 where invoices.id=:id
   		", ['id' => $id]);
        $voucer = DB::select("
   		SELECT id,dates,ifnull(voucers.debit,0) debit,pay_action_id  from voucers where invoice_id=:id and pay_action_id=1
   			", ['id' => $id]);
        return response()->json(['invoice' => $invoice, 'voucers' => $voucer]);
    }
}
