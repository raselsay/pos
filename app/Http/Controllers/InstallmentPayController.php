<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Validator;
use App\Voucer;
use Auth;
class InstallmentPayController extends Controller
{
    public function __construct(){
    	$this->middleware('auth');
    }

    public function Form(){
        $bank=DB::table('banks')->select('id','name')->get();
    	return view('pages.installment.installment_pay',compact('bank'));
    }
    public function getInsInvoice(Request $r){
    	if (!preg_match("/[^a-zA-Z0-9. ]/", $r->searchTerm)) {
            $data=DB::select("
                SELECT invoices.id,
                       customers.id customer_id,
                       customers.name,
                       cast(invoices.total_payable-((invoices.total_payable*ifnull(invoices.insmnt_pay_percent,0))/100) as decimal(20,2)) as total_payable,
                       ifnull(count(voucers.id),0) paid,
                       invoices.insmnt_total_days days
                 from invoices
            inner join customers on customers.id=invoices.customer_id and invoices.action_id=3
            left join voucers on invoices.id=voucers.invoice_id and voucers.pay_action_id=1
            where invoices.action_id=3  and customers.name like '%".$r->searchTerm."%' or invoices.id like '%".$r->searchTerm."%' or customers.id like '%".$r->searchTerm."%' group by invoices.id,voucers.invoice_id
            having paid<days limit 10");
            foreach ($data as $value) {
                $set_data[]=['id'=>$value->id."|".$value->customer_id,'text'=>$value->name."(".$value->total_payable.')'];
            }
            return $set_data;
        }
    }
    public function getInsAmmount($id=null){
    	$data['id']=$id;
    	$validator=Validator::make($data,[
    		'id'=>'required|max:15|min:1|regex:/^([0-9]+)$/',
    	]);
    	if ($validator->passes()) {
    		$total=DB::select("SELECT cast((invoices.total_payable-(invoices.total_payable*ifnull(invoices.insmnt_pay_percent,0))/100)/invoices.insmnt_total_days as decimal(20,2)) as total,cast(invoices.insmnt_total_days as int) total_days,ifnull(count(voucers.id),0) total_paid from invoices
                left join voucers on voucers.invoice_id=invoices.id and voucers.pay_action_id=1
            where invoices.action_id=3 and invoices.id=:id",['id'=>$id]);
            return response()->json($total);
    	}
    	return response()->json([$validator->getMessageBag()]);
    }
    public function Create(Request $r){
        $validator=Validator::make($r->all(),[
            'customer'=>'required|max:15|min:1|regex:/^([0-9]+)$/',
            'invoice'=>'required|max:15|min:1|regex:/^([0-9]+)$/',
            'transaction'=>'nullable|max:15|min:1|regex:/^([a-zA-Z0-9]+)$/',
            'ammount'=>'required|max:20|min:1|regex:/^([0-9.]+)$/',
            'bank'=>'required|max:10|min:1|regex:/^([0-9]+)$/',
            'date'=>'required|max:20|min:1|date_format:d-m-Y',
        ]);

        if ($validator->passes()) {
            $voucer=new Voucer;
            $voucer->dates=strtotime(strval($r->date));
            $voucer->bank_id=$r->bank;
            $voucer->nickname='customer';
            $voucer->data_id=$r->customer;
            $voucer->invoice_id=$r->invoice;
            $voucer->debit=$r->ammount;
            $voucer->pay_action_id=1;
            $voucer->user_id=Auth::user()->id;
            $voucer->save();
            if ($voucer==true) {
                return response()->json(['message'=>'Installment Payment Added','v_id'=>$voucer->id]);
            }
        }
        return response()->json(['error'=>$validator->getMessageBag()]);
    }
}
