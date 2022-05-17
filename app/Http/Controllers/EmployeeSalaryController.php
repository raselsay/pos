<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Validator;
use App\EmployeeSalaries;
use App\Notification;
use Auth;
use DataTables;
class EmployeeSalaryController extends Controller
{
    public function __construct(){
    	$this->middleware('auth');
    }
    public function Form(){
    	$data= date("d-m-Y",strtotime(date('d-m-Y')."-6 month"));
		for ($i=1; $i <=12 ; $i++) { 
			$months[]= date("F-Y",strtotime(date($data)."+".$i." month"));
		}
		if (request()->ajax()) {
            $total_bal = 500;
            $get = DB::table('employee_salaries')
            		->join('employees','employees.id','=','employee_salaries.employee_id')
            		->select('employee_salaries.id','employee_salaries.month','employee_salaries.dates','employees.name','employees.phone','employee_salaries.payable')->get();
            return DataTables::of($get)
                ->addIndexColumn()
                ->addColumn('action', function ($get) {
                    $button = '<div class="btn-group btn-group-toggle" data-toggle="buttons">
                       <button type="button" data-id="' . $get->id . '" class="btn btn-sm btn-primary rounded mr-1 edit" data-toggle="modal" data-target=""><i class="fas fa-eye"></i></button>
                       <button class="btn btn-danger btn-sm rounded delete" data-id="' . $get->id . '"><i class="fas fa-trash-alt"></i></button>
                    </div>';
                    return $button;
                })
                ->addColumn('date',function($get){
                	$date=date('d-m-Y',intval($get->dates));
                	return $date;
                })
                ->rawColumns(['action','date'])->make(true);
        }
    	return view('pages.employee.employee-salary',compact('months'));
    }
    public function Create(Request $r){
    	// return $r->all();
    	$validator = Validator::make($r->all(), [
            'date' => 'required|max:50|date_format:d-m-Y',
            'month' => 'required|max:50|unique:employee_salaries,month,'.$r->employee.'|regex:/^([0-9a-zA-Z,-. ]+)$/',
            'basic' => 'required|max:30|regex:/^([0-9.]+)$/',
            'tax' => 'nullable|max:50|regex:/^([0-9.]+)$/',
            'balance' => 'nullable|max:50|regex:/^([0-9.-]+)$/',
            'employee' => 'nullable|max:14|regex:/^([0-9]+)$/',
            'medical' => 'nullable|max:14|regex:/^([0-9.]+)$/',
            'bonus' => 'nullable|max:14|regex:/^([0-9.]+)$/',
            'over_time' => 'nullable|max:14|regex:/^([0-9.]+)$/',
            'provident' => 'nullable|max:14|regex:/^([0-9.]+)$/',
            'payable' => 'required|max:14|regex:/^([0-9.-]+)$/',
        ]);
        //for image
        if ($validator->passes()) {
            $bank = new EmployeeSalaries;
            $bank->dates = strtotime(strval($r->date));
            $bank->month = $r->month;
            $bank->basic = $r->basic;
            $bank->income_tax = $r->tax;
            $bank->balance = $r->balance;
            $bank->employee_id = $r->employee;
            $bank->medical = $r->medical;
            $bank->over_time = $r->over_time;
            $bank->bonus = $r->bonus;
            $bank->p_fund = $r->provident;
            $bank->payable = $r->payable;
            $bank->user_id = Auth::user()->id;
            $bank->save();
            return response()->json(['message' => 'Salary Added Success']);
        }
        return response()->json([$validator->getMessageBag()]);
    }
    public function EmployeeBalance($id){
    	 $data=DB::select("
    			SELECT (ifnull(sum(ifnull(employee_salaries.payable,0)),0)+ifnull(sum(if(
    			    			    			ifnull(employee_salaries.balance,0)<0,
    			    			    			abs(ifnull(employee_salaries.balance,0)),
    			    			    			-abs(ifnull(employee_salaries.balance,0))
    			    			    			)),0)
    			)-ifnull((
	    			SELECT sum(ifnull(voucers.credit,0))-sum(ifnull(voucers.debit,0)) 
	    			from names
	    			inner join voucers on voucers.category=names.id and names.table_name='employees'
	    			and voucers.data_id=:id
    			  ),0) total,employees.salary basic
    			from employee_salaries
    			inner join employees on employees.id=:id
    			where employee_salaries.employee_id=:id 
    		",['id'=>$id]);
    	return response()->json($data);
    }
    public function Delete($id){
    	$delete=DB::table('employee_salaries')->where('id',$id)->delete();
    	if ($delete){
            $notification = new Notification;
            $notification->details = 'Employee Salary No <strong>' . $id . '</strong>' . ' deleted by <strong>' . Auth::user()->name . '(' . Auth::user()->id . ')</strong>';
            $notification->action = 'delete';
            $save = $notification->save();
            if ($save) {
                return response()->json(['message' => 'Salary Deleted Success']);
            }
        }
    }
    
}
