<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use DataTables;
use DB;
use App\Notification;
use App\Invoice;
use App\Voucer;
use Auth;
class InstallmentStatusController extends Controller
{
    public function __construct(){
    	$this->middleware('auth');
    }
    public function Form(){
    	  if (request()->ajax()) {
            $get=DB::select("
              SELECT invoices.id,invoices.dates,customers.name,cast(invoices.total_payable-(((invoices.total_payable*ifnull(invoices.insmnt_pay_percent,0))/100)+ifnull(sum(voucers.debit),0)) as decimal(20,2)) due,cast(invoices.total_payable-((invoices.total_payable*ifnull(invoices.insmnt_pay_percent,0))/100) as decimal(20,2)) total_payable,ifnull(count(voucers.id),0) paid_inst,cast(invoices.insmnt_total_days-ifnull(count(voucers.id),0) as int) due_inst from invoices
              inner join customers on customers.id=invoices.customer_id
              left join voucers on voucers.invoice_id=invoices.id and voucers.pay_action_id=1
              where invoices.action_id=3 group by invoices.id,voucers.invoice_id
              order by invoices.id desc
              ");
        return DataTables::of($get)
              ->addIndexColumn()
              ->addColumn('action',function($get){
          $button  ='<div class="btn-group btn-group-toggle text-light" data-toggle="buttons">
                       <button type="button" class="btn btn-sm btn-primary rounded mr-1 edit" data-id="'.$get->id.'"><i class="fas fa-eye"></i></button>
                       <a class="btn btn-danger btn-sm rounded delete mr-1" data-id="'.$get->id.'"><i class="fas fa-trash-alt"></i></a>
                       <a class="btn btn-secondary btn-sm rounded print" data-id="' . $get->id . '"><i class="fas fa-print"></i></a>
                    </div>';
        return $button;
      })
      ->addColumn('date',function($get){
          return date('d-m-Y',$get->dates);
      })
      ->rawColumns(['action'])->make(true);
        }
        return view('pages.installment.installment_status');
    }
    public function getInvoice($id=null){
    	$invoice=DB::select("
SELECT invoices.id,invoices.dates,invoices.issue_dates,customers.name,invoices.total_item,invoices.total_payable,invoices.insmnt_type,invoices.insmnt_total_days,invoices.insmnt_pay_percent,ifnull(sum(voucers.debit),0)+ifnull(sum(voucers2.debit),0) debit,count(voucers2.id) from invoices
inner join customers on invoices.customer_id=customers.id
left join voucers on voucers.invoice_id=invoices.id and voucers.pay_action_id=0
left join voucers as voucers2 on voucers.invoice_id=invoices.id and voucers2.pay_action_id=1 where invoices.id=:id
   		",['id'=>$id]);
       return response()->json($invoice);
    }
    public function delete($id){
      $invoice=Invoice::find($id)->delete();
      $payment=Voucer::where('invoice_id',$id)->where('pay_action_id',1)->delete();
      if ($invoice){
          $notification = new Notification;
          $notification->details = 'Installment No <strong>' . $id . '</strong>' . ' deleted by <strong>' . Auth::user()->name . '(' . Auth::user()->id . ')</strong>';
          $notification->action = 'delete';
          $save = $notification->save();
          if ($save) {
              return response()->json(['message' => 'installment Deleted Success']);
          }
      }
    }
     public function getInstallmentData($id){
         $invoice = DB::table('invoices')
            ->join('customers', 'customers.id', '=', 'invoices.customer_id')
            ->leftJoin('voucers', 'invoices.payment_id', '=', 'voucers.id')
            ->selectRaw('invoices.id,invoices.customer_id,customers.name,customers.phone1,invoices.discount,invoices.vat,invoices.labour_cost,invoices.total_item,invoices.total_payable,invoices.total,invoices.dates,invoices.issue_dates,invoices.action_id,invoices.note,if(invoices.action_id=3,voucers.debit,voucers.credit) payment,invoices.insmnt_total_days,invoices.insmnt_type,invoices.insmnt_pay_percent,invoices.fine')
            ->where('invoices.id', $id)
            ->first();
        $sales = DB::select("select sales.id,sales.product_id,sales.invoice_id,products.product_name,sales.deb_qantity+sales.cred_qantity as qantity,sales.price,sales.discount from sales inner join products on products.id=sales.product_id where sales.invoice_id=:id order by sales.id asc", ['id' => $id]);

        $balance = $this->getCustomerBalance($invoice->customer_id);
        $invoice = json_encode($invoice);
        // $sales = json_encode($sales);
        return response()->json([
            'invoice' => $invoice,
            'sales' => $sales,
            'balance' => isset($balance[0]->total) ? $balance[0]->total : '',
        ]);
    }
    public function getCustomerBalance($id){
        if (!preg_match("/[^0-9]/", $id)) {
            $get = DB::select("
            SELECT
    cast(((t.Deposit+t.total_payablebacks)-(t.Expence+t.total_payable))+t.op_blnce as decimal(16,2)) as total
from(
    select
    ifnull(sum(ifnull(voucers.debit,0)),0) as Deposit,
    ifnull(sum(ifnull(voucers.credit,0)),0) as Expence,
    ifnull((select sum(total_payable) from invoices where customer_id=:id and (action_id=0 or action_id=3)),0) as total_payable,
    ifnull((select sum(total_payable) from invoices where customer_id=:id and action_id=2),0) as total_payablebacks,
    (select opening_balance from customers where id=:id) as op_blnce
    from voucers
    left join names on names.id=voucers.category  where (voucers.data_id=:id and voucers.nickname='customer') or (voucers.data_id=:id and names.table_name='customers')
    ) t", ['id' => $id]);
            return $get;
        } else {
            return ['data' => 'something wrong here'];
        }
    }
}
