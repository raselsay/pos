<?php

namespace App\Http\Controllers;

use App\Information;
use DB;
use Illuminate\Http\Request;
use Validator;

class RunningTotalController extends Controller {
    public function __construct() {
        $this->middleware('auth');
    }
    public function Form() {
        $categories = DB::table('names')->select('id', 'name')->where('stutus', 1)->get();
        $info = Information::select('company_name', 'adress', 'phone')->first();
        return view('pages.reports.running_total', compact('categories', 'info'));
    }
    public function CreateRunningTotal(Request $r) {
        $validator = Validator::make($r->all(), [
            'fromDate' => 'required|date_format:d-m-Y',
            'toDate' => 'required|date_format:d-m-Y',
            'id' => 'required|regex:/^([0-9]+)$/',
            'category' => 'required|regex:/^([0-9]+)$/',
        ]);
        if ($validator->passes()) {
            $fromDate = strtotime(strval($r->fromDate));
            $toDate = strtotime(strval($r->toDate));
            if ($r->category) {
                $category = DB::table('names')
                    ->select('table_name')
                    ->where('id', $r->category)
                    ->first();
                switch ($category->table_name) {
                case 'customers':
                    $previous = DB::select("
           SELECT
    cast(((t.Deposit+t.total_payablebacks)-(t.Expence+t.total_payable))+t.op_blnce as decimal(16,2)) as total
from(
    select
    ifnull(sum(ifnull(debit,0)),0) as Deposit,
    ifnull(sum(ifnull(credit,0)),0) as Expence,
    ifnull((select SUM(total_payable) from invoices where customer_id=:id and (action_id=0 or action_id=3) and dates<:fromDate),0) as total_payable,
    ifnull((select SUM(total_payable) from invoices where customer_id=:id and action_id=2 and dates<:fromDate),0) as total_payablebacks,
    (select opening_balance from customers where id=:id) as op_blnce
    from voucers
    left join names on voucers.category=names.id
      WHERE  (voucers.dates<:fromDate and voucers.data_id=:id and voucers.nickname='customer') or (voucers.dates<:fromDate and voucers.data_id=:id and names.table_name='customers')
    ) t", ['id' => $r->id, 'fromDate' => $fromDate]);
                    $current_blnce = DB::select("
            SELECT
    cast(((t.Deposit+t.total_payablebacks)-(t.Expence+t.total_payable))+t.op_blnce as decimal(16,2)) as total
from(
    select
    ifnull(sum(ifnull(debit,0)),0) as Deposit,
    ifnull(sum(ifnull(credit,0)),0) as Expence,
    ifnull((select SUM(total_payable) from invoices where customer_id=:id and (action_id=0 or action_id=3)),0) as total_payable,
    ifnull((select SUM(total_payable) from invoices where customer_id=:id and action_id=2),0) as total_payablebacks,
    (select opening_balance from customers where id=:id) as op_blnce
    from voucers
    left join names on voucers.category=names.id
      WHERE  (voucers.data_id=:id and voucers.nickname='customer') or (voucers.data_id=:id and names.table_name='customers')
    ) t", ['id' => $r->id]);
                    if ($previous[0]->total > 0) {
                        $dabit = abs($previous[0]->total);
                        $credit = 0;
                    } else {
                        $credit = abs($previous[0]->total);
                        $dabit = 0;
                    }
                    $get = DB::select("
              SELECT
                     t1.dates,
                     t1.invoice_id,
                     t1.product_name,
                     t1.id,
                     t1.deb_qantity,
                     t1.price,
                     t1.debit,
                     t1.credit
              FROM(
              select
                     sales.dates,
                     sales.invoice_id,
                     products.product_name as product_name,
                     concat('INV-',sales.invoice_id) as id,
                     sales.deb_qantity,
                     cast(sales.price-sales.price*sales.discount/100 as decimal(20,2)) price,
                     0 as debit,
                     (cast((sales.deb_qantity*sales.price)-((sales.price*sales.deb_qantity)*ifnull(discount,0))/100 as decimal(12,2))) as credit
                        FROM sales
                        inner join products
                        ON products.id=sales.product_id
                          WHERE sales.customer_id=:id and (action_id=0 or action_id=3)
                          and sales.dates>=:fromDate and sales.dates<=:toDate
                            UNION ALL
              SELECT
                     voucers.dates+0.2,
                     '',
                     '',
                     concat('V-',.voucers.id),
                     '',
                     '',
                     ifnull(voucers.debit,0) as debit,
                     ifnull(voucers.credit,0) as credit
                      FROM voucers
                      left join names on voucers.category=names.id
                        WHERE voucers.dates>=:fromDate and voucers.dates<=:toDate and ((voucers.data_id=:id and voucers.nickname='customer') or (voucers.data_id=:id and names.table_name='customers'))
                          UNION ALL
              SELECT '',
                     '',
                     'Prev-B',
                     '',
                     '',
                     '',
                     '" . $dabit . "',
                     '" . $credit . "'
                          UNION ALL
              SELECT
                      dates,
                      id+0.1,
                      '(cost+intrst)-dis',
                      concat('INV-',id),
                      '',
                      '',
                      cast(ifnull((total*discount)/100,0) as decimal(16,2)) as debit,
                      cast(ifnull((total*vat)/100,0)+ifnull(labour_cost,0)+ifnull((total*fine)/100,0)+ifnull(transport,0) as decimal(14,2)) as credit
                  from invoices
                      where customer_id=:id and (action_id=0 or action_id=3)
                    and  dates>=:fromDate and dates<=:toDate
                    UNION ALL
              SELECT
                    dates,
                    sales.invoice_id,
                    products.product_name,
                    concat('RINV-',sales.invoice_id),
                    sales.cred_qantity,
                    sales.price,
                    (cast((sales.cred_qantity*sales.price)-((sales.price*sales.cred_qantity)*ifnull(discount,0))/100 as decimal(12,2))) as dabit,
                    0 as credit
                    from sales inner join products on products.id=sales.product_id
                    where sales.customer_id=:id and action_id=2 and dates >=:fromDate and dates<=:toDate
                    UNION ALL
              SELECT
                    dates,
                    id+0.1,
                    'fine',
                    concat('RINV-',id),
                    '',
                    '',
                    cast(ifnull((total*discount)/100,0) as decimal(16,2)) as dabit,
                    cast(ifnull((total*vat)/100,0)+ifnull(labour_cost,0)+ifnull((total*fine)/100,0)+ifnull(transport,0) as decimal(14,2)) as credit
                    from invoices where customer_id=:id and action_id=2 and dates>=:fromDate and dates<=:toDate
              ) t1 order by t1.dates,t1.invoice_id", ['id' => $r->id, 'fromDate' => $fromDate, 'toDate' => $toDate]);
                    break;
                case 'suppliers':
                    $previous = DB::select("
              SELECT
((t.total_purchase+t.Deposit)-(t.total_purchase_backs+t.Expence))+t.opening_balance as total
from(
SELECT
    ifnull((select sum(ifnull(total_payable,0)) from invpurchases where supplier_id=:id and action_id=0 and dates<:fromDate),0) as total_purchase,
    ifnull((select sum(ifnull(total_payable,0)) from invpurchases where supplier_id=:id and action_id=2 and dates<:fromDate),0) as total_purchase_backs,
    (SELECT opening_balance from suppliers where id=:id) as opening_balance,
    ifnull(sum(ifnull(debit,0)),0) as Deposit,
    ifnull(sum(ifnull(credit,0)),0) as Expence
    from voucers
    left join names on voucers.category=names.id
      WHERE  (voucers.dates<:fromDate and voucers.data_id=:id and voucers.nickname='supplier') or (voucers.dates<:fromDate and voucers.data_id=:id and names.table_name='suppliers')
) t", ['id' => $r->id, 'fromDate' => $fromDate]);
                    $current_blnce = DB::select("
            SELECT
((t.total_purchase+t.Deposit)-(t.total_purchase_backs+t.Expence))+t.opening_balance as total
from(
SELECT
    ifnull((select sum(ifnull(total_payable,0)) from invpurchases where supplier_id=:id and action_id=0),0) as total_purchase,
    ifnull((select sum(ifnull(total_payable,0)) from invpurchases where supplier_id=:id and action_id=2),0) as total_purchase_backs,
    (SELECT opening_balance from suppliers where id=:id) as opening_balance,
    ifnull(sum(ifnull(debit,0)),0) as Deposit,
    ifnull(sum(ifnull(credit,0)),0) as Expence
    from voucers
    left join names on voucers.category=names.id
    WHERE  (voucers.data_id=:id and voucers.nickname='supplier') or (voucers.data_id=:id and names.table_name='suppliers')
      -- where category='supplier' and data_id=:id
    ) t ", ['id' => $r->id]);

                    if ($previous[0]->total < 0) {
                        $credit = abs($previous[0]->total);
                        $dabit = 0;
                    } else {
                        $dabit = abs($previous[0]->total);
                        $credit = 0;
                    }

                    $get = DB::select("
              SELECT t1.dates,
                     t1.product_name,
                     t1.invoice_id,
                     t1.id,
                     t1.deb_qantity,
                     t1.price,
                     t1.debit,
                     t1.credit
              FROM(
              select purchases.dates,
                     purchases.invoice_id,
                     products.product_name,
                     concat('INV-',purchases.invoice_id) as id,
                     purchases.deb_qantity,
                     purchases.price,
                     (cast(purchases.deb_qantity*purchases.price as decimal(12,2))) as debit,
                     0 as credit
                        FROM purchases
                        INNER JOIN products
                        ON products.id=purchases.product_id
                          WHERE supplier_id=:id and action_id=0
                          and dates>=:fromDate and dates<=:toDate
                            UNION ALL
              SELECT dates,
                     id+0.1,
                     't-port+lebour',
                     '',
                     '',
                     '',
                     ifnull(transport,0)+ifnull(labour_cost,0) as debit,
                     0 as credit
                     from invpurchases
                     where supplier_id=:id and action_id=0 and dates>=:fromDate and dates<=:toDate
                     UNION ALL
               select purchases.dates,
               purchases.invoice_id,
               products.product_name,
               '' as voucer_id,
               purchases.cred_qantity,
               purchases.price,
               0 as debit,
               (cast(purchases.cred_qantity*purchases.price as decimal(12,2))) as credit
                  FROM purchases
                  INNER JOIN products
                  ON products.id=purchases.product_id
                    WHERE purchases.supplier_id=:id and purchases.action_id=2
                    and purchases.dates>=:fromDate and purchases.dates<=:toDate
                      UNION ALL
                SELECT dates+0.1,
                  id+0.2,
                 '(t-port+lebour)-fine',
                 '',
                 '',
                 '',
                 cast(ifnull(total*fine/100,0) as decimal(16,2)) as dabit,
                 ifnull(transport,0)+ifnull(labour_cost,0)  as credit
                 from invpurchases
                 where supplier_id=:id and action_id=2 and dates>=:fromDate and dates<=:toDate
                 UNION ALL
              SELECT voucers.dates+0.2,
                     '',
                     '',
                     voucers.id,
                     '',
                     '',
                     ifnull(voucers.debit,0) as debit,
                     ifnull(voucers.credit,0) as credit
                      FROM voucers
                      left join names on voucers.category=names.id
                        WHERE voucers.dates>=:fromDate and voucers.dates<=:toDate and ((voucers.data_id=:id and voucers.nickname='supplier') or (voucers.data_id=:id and names.table_name='suppliers'))
                        -- WHERE data_id=:id and category='supplier'
                        -- and dates>=:fromDate and dates<=:toDate
                      UNION ALL
              SELECT '',
                     '',
                     'Prev-B',
                     '',
                     '',
                     '',
                     '" . $dabit . "',
                     '" . $credit . "'
              ) t1 order by t1.dates,t1.invoice_id", ['id' => intval($r->id), 'fromDate' => $fromDate, 'toDate' => $toDate]);
                }
            }
            return response()->json(['get' => $get, 'current_blnce' => $current_blnce, 'fromDate' => $fromDate, 'toDate' => $toDate]);
        }
        return response()->json(['errors'=>$validator->getMessageBag()]);
    }
}
