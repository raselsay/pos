<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Event;
use Validator;
use DB;
use DataTables;
use Auth;
class EventController extends Controller
{
    public function __construct(){
    	$this->middleware('auth');
    }
    public function Form(){
    	if (request()->ajax()) {
            $total_bal = 500;
            $get = DB::select("
           		SELECT customers.id,events.date,events.issue_date,concat(customers.name,'(',ifnull(customers.adress,'adress not found'),')') name,customers.phone1,spos.name as spo_name,events.description,users.name as username,((ifnull((select sum(voucers.debit-voucers.credit) from voucers
left join names on voucers.category=names.id
      WHERE  (voucers.data_id=customers.id and voucers.nickname='customer') or (voucers.data_id=customers.id and names.table_name='customers')
 -- where category='customer' and data_id=customers.id
),0)+ifnull((select sum(total_payable) from invoices where customer_id=customers.id and action_id=2),0))-ifnull((select sum(total_payable) from invoices where customer_id=customers.id and (action_id=0 or action_id=3)),0))+opening_balance as balance from customers
    left join events on customers.id=events.customer_id
    left join spos on spos.id=customers.spo_id
    left join users on users.id=events.user_id
            ");
            return DataTables::of($get)
                ->addIndexColumn()
                ->addColumn('date',function($get){
                	if ($get->date!=null) {
                        return $date=date('d-m-Y',$get->date);
                    }else{
                        return null;
                    }
                })
                ->addColumn('issue_date',function($get){
                    if ($get->issue_date!=null) {
                        return $date=date('d-m-Y',$get->issue_date);
                    }else{
                        return null;
                    }
                	
                })
                ->addColumn('action', function ($get) {
                    $button = '<div class="btn-group btn-group-toggle" data-toggle="buttons">
                       <button type="button" data-id="' . $get->id . '" class="btn btn-sm btn-primary rounded mr-1 edit" data-toggle="modal" data-target=""><i class="fas fa-eye"></i></button>
                       <button class="btn btn-danger btn-sm rounded delete" data-id="' . $get->id . '"><i class="fas fa-trash-alt"></i></button>
                    </div>';
                    return $button;
                })
                ->rawColumns(['date','issue_date','action'])->make(true);
        }
    	return view('pages.events.event');
    }
    public function Create(Request $r,$id){
    	// return $r->all();
    	$validator=Validator::make($r->all(),[
            'comment'=>'required|max:500|min:1',
    		'date'=>'required|date_format:d-m-Y',
    		'issue_date'=>'required|date_format:d-m-Y',
    	]);
    	if ($validator->passes()) {
            $events = Event::updateOrCreate(
                ['customer_id' => $id],
                [
                    'customer_id' => $id, 
                    'description' => $r->comment,
                    'date'=>strtotime($r->date),                    
                    'issue_date'=>strtotime($r->issue_date),
                    'user_id'=>Auth::user()->id,
                ]
                
);
      //       $events=Event::firstOrCreate(['customer_id' => $id]);
    		// $events->customer_id=$id;         
      //       $events->description=$r->comment;
    		// $events->date=strtotime($r->date);
    		// $events->issue_date=strtotime($r->issue_date);
    		// $events->user_id=Auth::user()->id;
    		// $events->save();
    		if($events) {
    			return response()->json(['message'=>'Event Added Success']);
    		}
    	}
    	return response()->json($validator->getMessageBag());
    }
    public function Delete($id){
    	$event=Event::find($id);
    	$event->status=0;
    	$event->user_id=Auth::user()->id;
    	$event->save();
    	if ($event) {
    		return response()->json(['message'=>"deleted Success"]);
    	}
    	return response()->json(['error'=>'something else']);
    }
    public function getData($id){
        $getData=Event::where('customer_id',$id)->get();
        if (count($getData)>0) {
            return response()->json($getData);
        }else{

        }
    }
}
