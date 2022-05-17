<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Voucer;
use Auth;
use DB;
use DataTables;
class FundTransferController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function Form(){
        if (request()->ajax()){
            $get=DB::select("
               SELECT voucers.id,voucers.dates,
               case when 
                    voucers.debit !=0.00 then concat(banks.name,' Received ',voucers.debit,' Taka From ',banks2.name)
                    when
                    voucers.credit !=0.00 then concat(banks.name,' Transfered ',voucers.credit,' Taka To ',banks2.name) else '' end as details
                         from voucers 
               inner join banks on banks.id=voucers.bank_id
               inner join banks as banks2 on banks2.id=voucers.data_id where voucers.pay_action_id=2;
                ");
            return DataTables::of($get)
              ->addIndexColumn()
              ->addColumn('date',function($get){
                $date=date('d-m-Y',$get->dates);
                return $date;
              })
              ->rawColumns(['date'])->make(true);
            }
           return view('pages.banks.fund_transfer');
    }
    public function Transfer(Request $r){
        $validation=Validator::make($r->all(),[
            'from'     => 'required|max:20|regex:/^([0-9]+)$/',
            'to'       => 'required|max:20|regex:/^([0-9]+)$/',
            'ammount'  => 'required|max:20|regex:/^([0-9]+)$/',
            'details'  => 'nullable|max:500',
        ]);
      
        if ($validation->passes()) {
            $voucer=new Voucer;
            $voucer->bank_id=$r->from;
            $voucer->dates=strtotime(strval(Date('d-m-Y')));
            $voucer->nickname='Balance Transfered';
            $voucer->data_id=$r->to;
            $voucer->credit=$r->ammount;
            $voucer->pay_action_id=2;
            $voucer->user_id=Auth::user()->id;
            $voucer->save();
            if ($voucer==true) {
                $voucer=new Voucer;
                $voucer->bank_id=$r->to;
                $voucer->dates=strtotime(strval(Date('d-m-Y')));
                $voucer->nickname='Balance Received';
                $voucer->data_id=$r->from;
                $voucer->debit=$r->ammount;
                $voucer->pay_action_id=2;
                $voucer->transaction=$r->transaction;
                $voucer->user_id=Auth::user()->id;
                $voucer->save();
                if ($voucer==true) {
                    return response()->json(['message'=>'Banks Transfer Success']);
                }
            }
        }
        return response()->json([$validation->getMessageBag()]);
    }
}
