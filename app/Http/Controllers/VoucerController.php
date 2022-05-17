<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use DB;
use Auth;
use Validator;
use DataTables;
use App\Voucer;
use App\VoucerDetails;
use App\Notification;
class VoucerController extends Controller
{
    public function __construct(){
    	$this->middleware('auth');
    }

    public function ManageVoucer(){
      $names=DB::table('names')->select('id','name')->where('stutus',1)->get();
    	$banks=DB::table('banks')->select('id','name')->get();
    	if (request()->ajax()) {
        $get=DB::select("
        SELECT t.id,t.dates,t.bank_name,t.category,t.name,t.debit,t.credit
from (SELECT 
             voucers.id,
             voucers.dates,
             banks.name bank_name,
             concat(ifnull(names.name,''),ifnull(voucers.nickname,'')) category,
             concat(
             if(isnull(suppliers.name),'',concat(suppliers.name,'(',ifnull(suppliers.adress,'adress not found'),')')),
             if(isnull(customers.name),'',concat(customers.name,'(',ifnull(customers.adress,'adress not found'),')')),
             if(isnull(employees.name),'',concat(employees.name,'(',ifnull(employees.adress,'adress not found'),')')),
             if(isnull(transports.name),'',concat(transports.name,'(',ifnull(transports.adress,'adress not found'),')')),
             ifnull(namerelations.rel_name,'')) as name,
  ifnull(voucers.debit,0) as debit,
  ifnull(voucers.credit,0) as credit
       from voucers
          inner join names on voucers.category=names.id 
          left join banks on voucers.bank_id=banks.id
          left join suppliers on (voucers.category=names.id and names.table_name='suppliers')  and voucers.data_id=suppliers.id
          left join customers on (voucers.category=names.id and names.table_name='customers')  and voucers.data_id=customers.id 
          left join employees on (voucers.category=names.id and names.table_name='employees')  and voucers.data_id=employees.id 
          left join transports on (voucers.category=names.id and names.table_name='transports')  and voucers.data_id=transports.id
          left join namerelations on names.id=namerelations.name_id and voucers.data_id=namerelations.id
          
     ) t order by t.dates desc,t.id desc
        ");
          return DataTables::of($get)
          ->addIndexColumn()
          ->addColumn('dat',function($get){
          $dates  = date('d-m-Y',$get->dates);
          return $dates;
          })
          ->addColumn('v_id',function($get){
          $id='1'.str_pad($get->id, 9,'0',STR_PAD_LEFT);
          return $id;
          })
          ->addColumn('action', function ($get) {
                    $button = '<div class="btn-group btn-group-toggle" data-toggle="buttons">
                       <button type="button" data-id="' . $get->id . '" class="btn btn-sm btn-primary rounded mr-1 edit" data-toggle="modal" data-target=""><i class="fas fa-eye"></i></button>
                       <button class="btn btn-danger btn-sm rounded delete text-light mr-1" data-id="' . $get->id . '"><i class="fas fa-trash-alt"></i></button>
                       <button class="btn btn-success btn-sm rounded print text-light" data-id="' . $get->id . '"><i class="fas fa-print"></i></button>
                    </div>';
                    return $button;
                })
          ->rawColumns(['dat','action'])->make(true);
        }
        return view('pages.voucer.voucers',compact('names','banks'));
    }
    public function getNameData($data=null){
      $data=trim($data);
      $data=ucwords($data);
        if ($data=='Customer' or $data=='Supplier'){
            $passdata=DB::table(strtolower($data).'s')->select('id','name')->get();
            return $passdata;
        }else{
            $passdata=DB::table('names')
                      ->join('namerelations','names.id','=','namerelations.name_id')
                      ->select('namerelations.id','namerelations.rel_name as name')
                      ->where('names.name',$data)
                      ->get();
             return $passdata;
        }
    }
    public function insertVoucer(Request $r){
        $data['details']=array_combine(range(1,count(explode(',',$r->details[0]))),explode(',',$r->details[0]));
        $data['qantity']=array_combine(range(1,count(explode(',',$r->qantity[0]))),explode(',',$r->qantity[0]));
        $data['ammount']=array_combine(range(1,count(explode(',',$r->ammount[0]))),explode(',',$r->ammount[0]));
        $data['date']=$r->date;
        $data['category']=$r->category;
        $data['data']=$r->data;
        $data['payment_type']=$r->payment_type;
        $data['bank']=$r->bank;
        $data['debit']=$r->debit;
        $data['credit']=$r->credit;
        $data['total_ammount']=$r->total_ammount;
        // return $data;
        $validator=Validator::make($data,[
            'date'=>'required|max:10|min:10|date_format:d-m-Y',
            'category'=>'required|max:100|regex:/^([a-zA-Z0-9., ]+)$/',
            'data'=>'required|max:20|regex:/^([0-9]+)$/',
            'payment_type'=>'required|max:7|regex:/^([a-zA-Z]+)$/',
            'bank'=>'required|max:10|regex:/^([0-9]+)$/',
            'debit'=>'nullable|max:20|regex:/^([0-9]+)$/',
            'credit'=>'nullable|max:20|regex:/^([0-9]+)$/',
            'total_ammount'=>'required|min:1|numeric|not_in:0',
        ]);
        if ($validator->passes()){
            $voucer=new Voucer;
            $voucer->dates=strtotime(strval($r->date));
            $voucer->category=strtolower($r->category);
            $voucer->data_id=$r->data;
            $voucer->bank_id=$r->bank;
            if ($r->payment_type=='Deposit') {
              $voucer->debit=$r->total_ammount;
              $voucer->credit=0;
            }
            if ($r->payment_type=='Expence') {
              $voucer->credit=$r->total_ammount;
              $voucer->debit=0;
            }
            $voucer->user_id=Auth::user()->id;
            $voucer->save();
            if ($voucer) {
              for ($i=1; $i<=count($data['details']) ; $i++) { 
                  $voucerDetails=new VoucerDetails;
                  $voucerDetails->voucer_id=$voucer->id;
                  $voucerDetails->details=$data['details'][$i];
                  $voucerDetails->qantity=$data['qantity'][$i];
                  $voucerDetails->ammount=$data['ammount'][$i];
                  $voucerDetails->save();
              }
              return ['message'=>'Voucer Added Success','v_id'=>$voucer->id];
            }
            
        }
        return response()->json($validator->getMessageBag());
    }
    public function getVoucerData($id){
        $voucer=DB::select("
        SELECT t.id,t.dates,t.bank_name,t.category,t.name,t.Deposit,t.Expence
from (SELECT 
             voucers.id,
             voucers.dates,
             banks.name bank_name,
             concat(ifnull(names.name,''),ifnull(voucers.nickname,'')) category,
             concat(ifnull(concat(suppliers.name,'(',suppliers.phone,')'),''),ifnull(concat(customers.name,'(',customers.phone1,')'),''),ifnull(concat(transports.name,'(',transports.phone,')'),''),ifnull(concat(employees.name,'(',employees.phone,')'),''),ifnull(namerelations.rel_name,'')) as name,
  ifnull(voucers.debit,0) as Deposit,
  ifnull(voucers.credit,0) as Expence
       from voucers
          inner join names on voucers.category=names.id
          left join banks on voucers.bank_id=banks.id
          left join suppliers on (voucers.category=names.id and names.table_name='suppliers')  and voucers.data_id=suppliers.id 
          left join customers on (voucers.category=names.id and names.table_name='customers')  and voucers.data_id=customers.id
          left join employees on (voucers.category=names.id and names.table_name='employees')  and voucers.data_id=employees.id
          left join transports on (voucers.category=names.id and names.table_name='transports')  and voucers.data_id=transports.id
          left join namerelations on names.id=namerelations.name_id and voucers.data_id=namerelations.id
        where voucers.id=:id
     ) t
        ",['id'=>$id]);
        $voucerDetails=DB::table('voucer_details')->select('details','qantity','ammount')->where('voucer_id',$id)->get();
        return response()->json(['voucer'=>$voucer,'detail'=>$voucerDetails]);
    }
    public function Delete($id){
      $data=Voucer::find($id)->delete();
      $data=VoucerDetails::where('voucer_id',$id)->delete();
            if ($data==0){
                $notification = new Notification;
                $notification->details = 'Voucer No <strong>' . $id . '</strong>' . ' deleted by <strong>' . Auth::user()->name . '(' . Auth::user()->id . ')</strong>';
                $notification->action = 'delete';
                $save = $notification->save();
                if ($save) {
                    return response()->json(['message' => 'Voucer Deleted Success']);
                }
            }else{
              return response()->json(['error'=>'Something Wrong Here']);
            }
    }
}
