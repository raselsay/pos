<?php

namespace App\Http\Controllers;

use App\Information;
use DB;

class BuyerReportController extends Controller {
    public function __construct() {
        $this->middleware('auth');
    }
    public function Form() {
        $info = Information::select('company_name', 'adress', 'phone')->first();
        return view('pages.reports.buyer.buyerlist', compact('info'));
    }
    public function BuyerList() {
        $data = DB::table('customers')->selectRaw("id,name,phone1,ifnull(adress,'not inserted') as adress,stutus")->get();

        return response()->json(['get' => $data]);
    }
    public function BuyerBalanceSheet() {
        $data = DB::select("
SELECT id,name,phone1,adress,((ifnull((select sum(voucers.debit-voucers.credit) from voucers
left join names on voucers.category=names.id
      WHERE  (voucers.data_id=customers.id and voucers.nickname='customer') or (voucers.data_id=customers.id and names.table_name='customers')
 -- where category='customer' and data_id=customers.id
),0)+ifnull((select sum(total_payable) from invoices where customer_id=customers.id and action_id=2),0))-ifnull((select sum(total_payable) from invoices where customer_id=customers.id and (action_id=0 or action_id=3)),0))+opening_balance as balance from customers
    ");
        return response()->json(['get' => $data]);
    }
    public function BuyerBlnceForm() {
        $info = Information::select('company_name', 'adress', 'phone')->first();
        return view('pages.reports.buyer.buyerbalancesheet', compact('info'));
    }
}
