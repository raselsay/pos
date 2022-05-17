<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Voucer;
use App\Invoice;
use App\Invpurchase;
use App\Keycheck;
use App\Customer;
use App\Supplier;
use DB;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }
    public function dashboard(){
        $CustomerBalance=$this->CustomerBalance()[0]->total;
        $customer=Customer::count();
        $supplier=Supplier::count();
        $installment=Invoice::where('action_id',3)->count();
        $deposit=DB::select("
                SELECT ifnull(sum(debit),0) debit
                    FROM voucers 
                    WHERE dates=:dates and (pay_action_id <> 2 OR pay_action_id is NULL);
            ",['dates'=>strtotime(date('d-m-Y'))]);
        $expence=DB::select("
                SELECT ifnull(sum(credit),0) credit
                    FROM voucers 
                    WHERE dates=:dates and (pay_action_id <> 2 OR pay_action_id is NULL);
            ",['dates'=>strtotime(date('d-m-Y'))]);
        $total_sales_ammount=Invoice::where('dates',strtotime(date('d-m-Y')))->sum('total_payable');
        $saleChart=$this->SaleChart();
        $pieSaleChart=$this->PieSaleChart();
        $purchasePieChart=$this->PurchasePieChart();
        $access_days=Keycheck::select('todate')->first();
        if (isset($access_days->todate)) {
            $date =date('d-m-Y',$access_days->todate);
        }else{
            $date='Not Exist';
        }
        return response()->json(['deposit'=>$deposit[0]->debit,'expence'=>$expence[0]->credit,'total_sales'=>$total_sales_ammount,'access_days'=>$date,'customer'=>$customer,'supplier'=>$supplier,'installment'=>$installment,'customer_balance'=>$CustomerBalance,'sale_chart'=>$saleChart,'pie_sale_chart'=>$pieSaleChart,'purchase_pie_chart'=>$purchasePieChart]);
    }
    public function CustomerBalance(){
        $get = DB::select("
            SELECT
    cast(((t.Deposit+t.total_payablebacks)-(t.Expence+t.total_payable))+t.op_blnce as decimal(16,2)) as total
from(
    select
    ifnull(sum(ifnull(voucers.debit,0)),0) as Deposit,
    ifnull(sum(ifnull(voucers.credit,0)),0) as Expence,
    ifnull((select sum(total_payable) from invoices where (action_id=0 or action_id=3)),0) as total_payable,
    ifnull((select sum(total_payable) from invoices where action_id=2),0) as total_payablebacks,
    ifnull((select sum(opening_balance) from customers),0) as op_blnce
    from voucers
    left join names on names.id=voucers.category  where voucers.nickname='customer' or names.table_name='customers'
    ) t");
        return $get;
    }
    public function getVoucerFormData(){
        $banks=DB::table('banks')->select('id','name')->get();
        $category=DB::table('names')->select('id','name')->get();
        return ['category'=>$category,'banks'=>$banks];
    }
    public function SaleChart(){
        $date= date('Y-m-d', strtotime('-30 days'));
        for ($i=1; $i <=30 ; $i++) {
        $strtotime=strtotime(date('d-m-Y',strtotime($date .' +'.$i.' days')));
        $query=DB::table('invoices')->selectRaw('ifnull(sum(total_payable),0) payable')->where('dates',strval($strtotime))->first();
        $value[date('M-d',$strtotime)]=$query->payable;
        }
        return $value;
    }
    public function PieSaleChart(){
        $date=strtotime(date('d-m-Y'));
        $fromDate=strtotime(date('d-m-Y',strtotime('-7 days')));
        $normalSale=Invoice::where('action_id',0)->where('dates','>=',$fromDate)->where('dates','<=',$date)->sum('total_payable');
        $advanceSale=Invoice::where('action_id',1)->where('dates','>=',$fromDate)->where('dates','<=',$date)->sum('total_payable');
        $saleReturn=Invoice::where('action_id',2)->where('dates','>=',$fromDate)->where('dates','<=',$date)->sum('total_payable');
        $installment=Invoice::where('action_id',3)->where('dates','>=',$fromDate)->where('dates','<=',$date)->sum('total_payable');
        return ['Sale'=>$normalSale,'Advance_Sale'=>$advanceSale,'Sale_Return'=>$saleReturn,'Installment'=>$installment];
    }
    public function PurchasePieChart(){
        $date=strtotime(date('d-m-Y'));
        $fromDate=strtotime(date('d-m-Y',strtotime('-7 days')));
        $normalPurchase=Invpurchase::where('action_id',0)->where('dates','>=',$fromDate)->where('dates','<=',$date)->sum('total_payable');
        $advancePurchase=Invpurchase::where('action_id',1)->where('dates','>=',$fromDate)->where('dates','<=',$date)->sum('total_payable');
        $purchaseReturn=Invpurchase::where('action_id',2)->where('dates','>=',$fromDate)->where('dates','<=',$date) ->sum('total_payable');
        return ['Purchase'=>$normalPurchase,'Advance_Purchase'=>$advancePurchase,'Purchase_Return'=>$purchaseReturn];
    }
}
